<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class SellerEmailVerificationController extends Controller
{
    public function notice(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) return redirect()->route('seller.dashboard');
        return view('auth.verify-email');
    }
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect()->route('seller.dashboard')->with('success', 'Votre adresse e-mail est validée. Bienvenue !');
    }
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) return redirect()->route('seller.dashboard');
        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (\Throwable $exception) {
            return back()->withErrors(['mail' => 'L’envoi a échoué. Réessayez plus tard ou contactez la plateforme.']);
        }
        return back()->with('success', 'Un nouveau lien de validation vous a été envoyé.');
    }
}
