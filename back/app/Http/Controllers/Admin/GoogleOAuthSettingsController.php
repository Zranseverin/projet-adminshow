<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoogleOAuthSetting;
use Illuminate\Http\Request;

class GoogleOAuthSettingsController extends Controller
{
    public function edit()
    {
        $settings = GoogleOAuthSetting::current();
        if (! $settings->exists && blank($settings->redirect_uri)) {
            $settings->redirect_uri = route('seller.google.callback');
        }

        return view('admin.google-settings', [
            'settings' => $settings,
            'hasSecret' => filled($settings->client_secret),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|string|max:255',
            'client_secret' => 'nullable|string|max:2048',
            'redirect_uri' => 'required|url|max:2048',
        ]);

        $settings = GoogleOAuthSetting::current();
        if (blank($data['client_secret'] ?? null)) {
            unset($data['client_secret']);
        }

        $settings->fill($data)->save();

        return back()->with('success', 'La configuration Google OAuth a été enregistrée.');
    }
}
