<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailSetting extends Model
{
    protected $guarded = ['id', 'seller_id'];
    protected $hidden = ['password'];
    protected function casts(): array { return ['password' => 'encrypted']; }

    public static function current(): self
    {
        return static::whereNull('seller_id')->first() ?? new static([
            'host' => config('mail.mailers.smtp.host'), 'port' => config('mail.mailers.smtp.port'),
            'encryption' => config('mail.mailers.smtp.scheme') === 'smtps' ? 'ssl' : 'tls',
            'username' => config('mail.mailers.smtp.username'),
            'from_address' => config('mail.from.address'), 'from_name' => config('mail.from.name'),
        ]);
    }

    public static function forSeller(Seller $seller): self
    {
        $settings = static::where('seller_id', $seller->id)->first() ?? new static([
            'host' => '', 'port' => 587, 'encryption' => 'tls', 'username' => '',
            'from_address' => '', 'from_name' => $seller->name,
        ]);
        $settings->seller_id = $seller->id;
        return $settings;
    }

    public static function forRecipient(User $user): self
    {
        if ($user->member_seller_id) {
            return static::where('seller_id', $user->member_seller_id)->first() ?? static::current();
        }
        return static::current();
    }

    public static function configureMailer(?self $settings = null): string
    {
        if (app()->environment('testing')) return config('mail.default');
        $settings ??= static::current();
        $name = $settings->seller_id ? 'seller_smtp_'.$settings->seller_id : 'seller_smtp';
        config(['mail.mailers.'.$name => [
            'transport' => 'smtp', 'scheme' => $settings->encryption === 'ssl' ? 'smtps' : 'smtp',
            'host' => $settings->host, 'port' => (int) $settings->port,
            'username' => $settings->username,
            'password' => $settings->password ?? ($settings->seller_id ? null : config('mail.mailers.smtp.password')),
            'require_tls' => true, 'timeout' => 15,
        ]]);
        app('mail.manager')->purge($name);
        return $name;
    }
}
