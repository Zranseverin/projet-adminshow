<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        return view('profile.show', ['user' => $request->user()->load(['role', 'genre'])]);
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'genres' => Genre::orderBy('libelle')->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_complet' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'telephone' => ['nullable', 'string', 'max:50'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'genre_id' => ['nullable', 'exists:genres,id'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (filled($data['password'] ?? null)) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        unset($data['current_password']);
        $emailChanged = $user->email !== $data['email'];
        $user->fill($data);
        if ($emailChanged && $user->account_type === 'seller') $user->email_verified_at = null;
        $user->save();
        if ($emailChanged && $user->account_type === 'seller') {
            try { $user->sendEmailVerificationNotification(); } catch (\Throwable $exception) {
                return redirect()->route('verification.notice')->withErrors(['mail' => 'Impossible d’envoyer le lien. Vous pouvez réessayer ci-dessous.']);
            }
            return redirect()->route('verification.notice');
        }

        return redirect()->route('profile.show')->with('success', 'Votre profil a été mis à jour.');
    }
}
