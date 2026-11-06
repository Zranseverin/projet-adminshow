<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MailSetting;
use App\Notifications\VerifySellerEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SellerEmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_seller_must_verify_a_signed_link_before_accessing_the_shop(): void
    {
        Notification::fake();
        $this->post('/devenir-vendeur', ['name'=>'Alice', 'shop_name'=>'Boutique', 'email'=>'alice@example.com', 'phone'=>'0700000000', 'password'=>'password12345', 'password_confirmation'=>'password12345'])->assertRedirect(route('verification.notice'));
        $user = User::where('email', 'alice@example.com')->firstOrFail();
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifySellerEmail::class);
        $this->get(route('seller.dashboard'))->assertRedirect(route('verification.notice'));
        $this->get(route('verification.notice'))->assertOk();
        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), ['id'=>$user->id,'hash'=>sha1($user->email)]);
        $this->get($url.'&tampered=1')->assertForbidden();
        $this->get($url)->assertRedirect(route('seller.dashboard'));
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->get(route('seller.dashboard'))->assertOk();
    }

    public function test_mail_configuration_is_admin_only_and_password_is_encrypted_and_retained(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['account_type'=>'seller'])->save();
        $this->actingAs($user)->get(route('admin.mail-settings.edit'))->assertForbidden();
        $user->forceFill(['account_type'=>'admin'])->save();
        $this->get(route('admin.mail-settings.edit'))->assertOk();
        $data = ['host'=>'smtp.example.com','port'=>587,'encryption'=>'tls','username'=>'sender@example.com','password'=>'test-smtp-secret','from_address'=>'sender@example.com','from_name'=>'Zran Market'];
        $this->put(route('admin.mail-settings.update'),$data)->assertSessionHasNoErrors()->assertRedirect();
        $settings = MailSetting::firstOrFail();
        $this->assertSame('test-smtp-secret', $settings->password);
        $this->assertNotSame('test-smtp-secret', $settings->getRawOriginal('password'));
        $this->get(route('admin.mail-settings.edit'))->assertDontSee('test-smtp-secret');
        $data['password'] = '';
        $this->put(route('admin.mail-settings.update'),$data)->assertSessionHasNoErrors();
        $this->assertSame('test-smtp-secret', $settings->fresh()->password);
    }

    public function test_verification_mail_contains_the_signed_link_and_sender(): void
    {
        $user = User::factory()->create();
        $message = (new VerifySellerEmail)->toMail($user);
        $this->assertStringContainsString('/email/verify/'.$user->id.'/', $message->actionUrl);
        $this->assertStringContainsString('signature=', $message->actionUrl);
        $this->assertSame('Valider mon adresse e-mail', $message->actionText);
        $html = view($message->view['html'], $message->viewData)->render();
        $this->assertStringContainsString('Votre boutique', $html);
        $this->assertStringContainsString(e($message->actionUrl), $html);
        $this->assertStringNotContainsString('Laravel', $html);
        $this->assertStringNotContainsString("If you're having trouble", $html);
        $text = view($message->view['text'], $message->viewData)->render();
        $this->assertStringContainsString('Valider mon adresse e-mail', $text);
    }
}
