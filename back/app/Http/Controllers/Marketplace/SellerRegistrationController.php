<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SellerRegistrationController extends Controller
{
    public function create()
    {
        return view('marketplace.register', ['googlePending' => (bool) session('seller_google')]);
    }

    public function redirectToGoogle()
    {
        $clientId = config('services.google.client_id');
        $redirect = config('services.google.redirect');
        abort_unless(filled($clientId) && filled(config('services.google.client_secret')), 503,
            'La création de compte Google n’est pas encore configurée.');

        $state = Str::random(40);
        session(['seller_google_oauth_state' => $state]);
        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirect,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'access_type' => 'online',
            'prompt' => 'select_account',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?'.$query);
    }

    public function handleGoogleCallback(Request $request)
    {
        $expectedState = (string) session('seller_google_oauth_state', '');
        abort_unless($request->filled('code') && $expectedState !== ''
            && hash_equals($expectedState, (string) $request->query('state')), 403,
            'La vérification de la connexion Google a échoué. Réessayez.');
        $request->session()->forget('seller_google_oauth_state');

        $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code' => $request->query('code'),
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri' => config('services.google.redirect'),
            'grant_type' => 'authorization_code',
        ]);
        abort_unless($tokenResponse->successful() && $tokenResponse->json('access_token'), 422,
            'Google n’a pas pu confirmer votre compte. Veuillez réessayer.');

        $profileResponse = Http::withToken($tokenResponse->json('access_token'))
            ->get('https://www.googleapis.com/oauth2/v2/userinfo');
        $profile = $profileResponse->json();
        abort_unless($profileResponse->successful() && filter_var($profile['email'] ?? null, FILTER_VALIDATE_EMAIL)
            && ($profile['verified_email'] ?? false), 422,
            'Google doit confirmer votre adresse e-mail avant la création du compte.');

        $existing = User::where('email', $profile['email'])->first();
        if ($existing) {
            abort_unless($existing->account_type === 'seller' && ! $existing->member_seller_id, 409,
                'Cette adresse est déjà associée à un autre type de compte. Connectez-vous avec votre méthode habituelle.');
            Auth::login($existing);
            $request->session()->regenerate();
            return redirect()->route($existing->hasVerifiedEmail() ? 'seller.dashboard' : 'verification.notice');
        }

        session(['seller_google' => ['name' => Str::limit(trim($profile['name'] ?? ''), 160, ''), 'email' => $profile['email']]]);
        return redirect()->route('seller.register');
    }

    public function completeGoogleRegistration(Request $request)
    {
        $google = $request->session()->get('seller_google');
        abort_unless(is_array($google) && filled($google['email'] ?? null), 419, 'Votre session Google a expiré. Recommencez la création du compte.');
        $data = $request->validate([
            'name' => 'required|string|max:160', 'shop_name' => 'required|string|max:160', 'phone' => 'required|string|max:40',
        ]);
        abort_if(User::where('email', $google['email'])->exists(), 409, 'Un compte existe déjà avec cette adresse e-mail. Connectez-vous.');

        $user = DB::transaction(function () use ($data, $google) {
            $user = new User(['name' => $data['name'], 'email' => $google['email'], 'password' => Str::random(64)]);
            $user->account_type = 'seller';
            $user->email_verified_at = now();
            $user->save();
            Seller::create(['user_id' => $user->id, 'name' => $data['shop_name'],
                'slug' => (Str::slug($data['shop_name']) ?: 'boutique').'-'.Str::lower(Str::random(10)), 'phone' => $data['phone']]);
            return $user;
        });

        $request->session()->forget('seller_google');
        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->route('seller.dashboard')->with('success', 'Votre compte vendeur Google est créé.');
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

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $exception) {
            return redirect()->route('verification.notice')->withErrors(['mail' => 'Votre compte est créé, mais le mail n’a pas pu être envoyé. Réessayez avec le bouton ci-dessous.']);
        }
        return redirect()->route('verification.notice')->with('success', 'Un lien de validation a été envoyé à votre adresse e-mail.');
    }
}
