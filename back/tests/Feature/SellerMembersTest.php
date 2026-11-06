<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Seller;
use App\Models\Product;
use App\Notifications\VerifySellerEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SellerMembersTest extends TestCase
{
    use RefreshDatabase;

    private function shop(): Seller
    {
        $owner = User::factory()->create();
        $owner->forceFill(['account_type'=>'seller'])->save();
        return Seller::create(['user_id'=>$owner->id,'name'=>'Boutique '.$owner->id,'slug'=>'boutique-'.$owner->id,'phone'=>'0700000000']);
    }

    private function member(Seller $seller): User
    {
        $member = User::factory()->create();
        $member->forceFill(['account_type'=>'seller','member_seller_id'=>$seller->id,'member_is_active'=>true])->save();
        return $member;
    }

    public function test_owner_can_create_a_member_without_granting_owner_or_admin_rights(): void
    {
        Notification::fake();
        $seller = $this->shop();
        $other = $this->shop();
        $this->actingAs($seller->user)->get(route('seller.members.index'))->assertOk()->assertSee('Ajouter un membre');
        $this->post(route('seller.members.store'), ['name'=>'Employé','email'=>'member@example.com','password'=>'MemberPassword123','password_confirmation'=>'MemberPassword123','account_type'=>'admin','member_seller_id'=>$other->id,'email_verified_at'=>now()->toDateTimeString()])->assertSessionHasNoErrors();
        $member = User::where('email','member@example.com')->firstOrFail();
        $this->assertSame($seller->id, $member->member_seller_id);
        $this->assertSame('seller', $member->account_type);
        $this->assertFalse($member->ownsSeller());
        $this->assertNull($member->email_verified_at);
        $this->assertTrue(Hash::check('MemberPassword123', $member->password));
        $this->assertSame(2, Seller::count());
        Notification::assertSentTo($member, VerifySellerEmail::class);
        $this->actingAs($member)->get(route('seller.products.index'))->assertRedirect(route('verification.notice'));
    }

    public function test_member_is_limited_to_own_shop_and_cannot_manage_members_or_settings(): void
    {
        $seller = $this->shop(); $other = $this->shop(); $member = $this->member($seller);
        $own = Product::create(['seller_id'=>$seller->id,'name'=>'Produit équipe','price_minor'=>10000,'stock'=>5,'status'=>'draft']);
        $foreign = Product::create(['seller_id'=>$other->id,'name'=>'Produit privé autre boutique','price_minor'=>10000,'stock'=>5,'status'=>'draft']);
        $this->actingAs($member)->get(route('seller.products.index',['seller_id'=>$other->id]))->assertOk()->assertSee('Produit équipe')->assertDontSee('Produit privé autre boutique')->assertDontSee('href="'.route('seller.members.index').'"',false);
        $this->get(route('seller.products.edit',$own))->assertOk();
        $this->get(route('seller.products.edit',$foreign))->assertNotFound();
        $this->get(route('seller.inventory'))->assertOk()->assertDontSee('Produit privé autre boutique');
        $this->get(route('seller.orders'))->assertOk();
        $this->get(route('seller.members.index'))->assertForbidden();
        $this->post(route('seller.members.store'),[])->assertForbidden();
        $this->get(route('seller.shop'))->assertForbidden();
        $this->put(route('seller.shop'),['name'=>'Hacked'])->assertForbidden();
        $this->get(route('admin.mail-settings.edit'))->assertForbidden();
        $this->put(route('seller.inventory.update',$own),['type'=>'entry','quantity'=>2,'reason'=>'Réception membre'])->assertSessionHasNoErrors();
        $this->assertSame(7,$own->fresh()->stock);
        $this->assertDatabaseHas('product_stock_movements',['product_id'=>$own->id,'user_id'=>$member->id]);
        $this->put(route('seller.inventory.update',$foreign),['type'=>'entry','quantity'=>2,'reason'=>'Interdit'])->assertNotFound();
    }

    public function test_owner_can_only_suspend_own_members_and_suspension_blocks_existing_sessions(): void
    {
        $seller = $this->shop(); $other = $this->shop();
        $member = $this->member($seller); $foreign = $this->member($other);
        $this->actingAs($seller->user)->put(route('seller.members.update',$foreign),['is_active'=>false])->assertNotFound();
        $this->put(route('seller.members.update',$seller->user),['is_active'=>false])->assertNotFound();
        $this->put(route('seller.members.update',$member),['is_active'=>false])->assertSessionHasNoErrors();
        $this->assertFalse($member->fresh()->member_is_active);
        $this->actingAs($member->fresh())->get(route('seller.products.index'))->assertForbidden();
        $this->actingAs($seller->user)->put(route('seller.members.update',$member),['is_active'=>true])->assertSessionHasNoErrors();
        $this->actingAs($member->fresh())->get(route('seller.products.index'))->assertOk();
        $seller->update(['is_active'=>false]);
        $this->actingAs($member->fresh())->get(route('seller.products.index'))->assertForbidden();
    }

    public function test_each_owner_has_private_mail_settings_and_members_cannot_change_them(): void
    {
        $seller = $this->shop(); $other = $this->shop();
        $member = $this->member($seller);
        $data = ['host'=>'smtp.shop.example','port'=>587,'encryption'=>'tls','username'=>'team@shop.example','password'=>'private-shop-password','from_address'=>'team@shop.example','from_name'=>'Ma boutique','seller_id'=>$other->id];
        $this->actingAs($seller->user)->get(route('seller.mail-settings.edit'))->assertOk()->assertDontSee('momine@severinzran.ci');
        $this->put(route('seller.mail-settings.update'),$data)->assertSessionHasNoErrors();
        $settings = \App\Models\MailSetting::where('seller_id',$seller->id)->firstOrFail();
        $this->assertSame('private-shop-password',$settings->password);
        $this->assertNotSame('private-shop-password',$settings->getRawOriginal('password'));
        $this->assertSame(0,\App\Models\MailSetting::where('seller_id',$other->id)->count());
        $this->get(route('seller.mail-settings.edit'))->assertSee('team@shop.example')->assertDontSee('private-shop-password');
        $this->actingAs($other->user)->get(route('seller.mail-settings.edit'))->assertOk()->assertDontSee('team@shop.example');
        $this->actingAs($member)->get(route('seller.mail-settings.edit'))->assertForbidden();
        $this->put(route('seller.mail-settings.update'),$data)->assertForbidden();
        $data['password'] = '';
        $this->actingAs($seller->user)->put(route('seller.mail-settings.update'),$data)->assertSessionHasNoErrors();
        $this->assertSame('private-shop-password',$settings->fresh()->password);
        $this->actingAs($other->user)->put(route('seller.mail-settings.update'),$data)->assertSessionHasErrors('password');
        $message = (new VerifySellerEmail)->toMail($member);
        $this->assertSame(['team@shop.example','Ma boutique'],$message->from);
        $ownerMessage = (new VerifySellerEmail)->toMail($seller->user);
        $this->assertNotSame('team@shop.example',$ownerMessage->from[0]);
        $this->assertNull(\App\Models\MailSetting::current()->seller_id);
    }
}
