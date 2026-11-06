<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SellerMemberController extends Controller
{
    private function owner(Request $request)
    {
        abort_unless($request->user()->ownsSeller(), 403);
        return $request->user()->seller;
    }

    public function index(Request $request)
    {
        $seller = $this->owner($request);
        return view('marketplace.members', ['members' => $seller->members()->latest()->paginate(15)]);
    }

    public function store(Request $request)
    {
        $seller = $this->owner($request);
        $data = $request->validate([
            'name' => 'required|string|max:160', 'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:10|confirmed',
        ]);
        $member = new User($data);
        $member->account_type = 'seller';
        $member->member_seller_id = $seller->id;
        $member->member_is_active = true;
        $member->save();
        try {
            $member->sendEmailVerificationNotification();
        } catch (\Throwable $exception) {
            return back()->with('success', 'Compte membre créé.')->withErrors(['mail' => 'Le mail de validation n’a pas pu être envoyé. Le membre pourra demander un nouvel envoi après connexion.']);
        }
        return back()->with('success', 'Compte membre créé. Un e-mail de validation lui a été envoyé.');
    }

    public function update(Request $request, User $member)
    {
        $seller = $this->owner($request);
        abort_unless($member->member_seller_id === $seller->id, 404);
        $data = $request->validate(['is_active' => 'required|boolean']);
        $member->member_is_active = $data['is_active'];
        $member->save();
        return back()->with('success', $member->member_is_active ? 'Accès du membre réactivé.' : 'Accès du membre suspendu.');
    }
}
