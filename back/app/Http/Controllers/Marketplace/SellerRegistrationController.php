<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SellerRegistrationController extends Controller
{
    public function create()
    {
        return view('marketplace.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:160', 'shop_name' => 'required|string|max:160',
            'email' => 'required|email|max:255|unique:users,email', 'phone' => 'required|string|max:40',
            'password' => 'required|string|min:10|confirmed',
        ]);
        $user = DB::transaction(function () use ($data) {
            $user = new User(['name' => $data['name'], 'email' => $data['email'], 'password' => $data['password']]);
            $user->account_type = 'seller';
            $user->save();
            Seller::create(['user_id' => $user->id, 'name' => $data['shop_name'],
                'slug' => (Str::slug($data['shop_name']) ?: 'boutique').'-'.Str::lower(Str::random(10)), 'phone' => $data['phone']]);

            return $user;
        });
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('seller.products.index');
    }
}
