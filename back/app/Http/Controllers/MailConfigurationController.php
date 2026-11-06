<?php

namespace App\Http\Controllers;

use App\Models\MailSetting;
use Illuminate\Http\Request;

class MailConfigurationController extends Controller
{
    public function edit() { return view('admin.mail-settings', ['settings' => MailSetting::current()]); }
    public function update(Request $request)
    {
        $data = $request->validate([
            'host' => 'required|string|max:255|regex:/^[a-zA-Z0-9.-]+$/',
            'port' => 'required|integer|in:465,587', 'encryption' => 'required|in:tls,ssl',
            'username' => 'required|string|max:255', 'password' => 'nullable|string|max:1024',
            'from_address' => 'required|email|max:255', 'from_name' => 'required|string|max:160',
        ]);
        if (blank($data['password'] ?? null)) unset($data['password']);
        $settings = MailSetting::current();
        $settings->fill($data)->save();
        return back()->with('success', 'Configuration mail enregistrée.');
    }
}
