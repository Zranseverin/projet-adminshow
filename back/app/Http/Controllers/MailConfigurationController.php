<?php

namespace App\Http\Controllers;

use App\Models\MailSetting;
use Illuminate\Http\Request;

class MailConfigurationController extends Controller
{
    public function edit() { return view('admin.mail-settings', ['settings' => MailSetting::current()]); }
    public function update(Request $request)
    {
        return $this->save($request, MailSetting::current());
    }

    public function sellerEdit(Request $request)
    {
        abort_unless($request->user()->ownsSeller(), 403);
        return view('marketplace.mail-settings', ['settings' => MailSetting::forSeller($request->user()->seller)]);
    }

    public function sellerUpdate(Request $request)
    {
        abort_unless($request->user()->ownsSeller(), 403);
        return $this->save($request, MailSetting::forSeller($request->user()->seller));
    }

    private function save(Request $request, MailSetting $settings)
    {
        $data = $request->validate([
            'host' => 'required|string|max:255|regex:/^[a-zA-Z0-9.-]+$/',
            'port' => 'required|integer|in:465,587', 'encryption' => 'required|in:tls,ssl',
            'username' => 'required|string|max:255', 'password' => ($settings->seller_id && ! $settings->exists ? 'required' : 'nullable').'|string|max:1024',
            'from_address' => 'required|email|max:255', 'from_name' => 'required|string|max:160',
        ]);
        if (blank($data['password'] ?? null)) unset($data['password']);
        $settings->fill($data)->save();
        return back()->with('success', 'Configuration mail enregistrée.');
    }
}
