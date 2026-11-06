<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailSetting extends Model
{
    protected $guarded = ['id'];
    protected $hidden = ['password'];
    protected function casts(): array { return ['password' => 'encrypted']; }

    public static function current(): self
    {
        return static::first() ?? new static([
            'host' => config('mail.mailers.smtp.host'), 'port' => config('mail.mailers.smtp.port'),
            'encryption' => config('mail.mailers.smtp.scheme') === 'smtps' ? 'ssl' : 'tls',
            'username' => config('mail.mailers.smtp.username'),
            'from_address' => config('mail.from.address'), 'from_name' => config('mail.from.name'),
        ]);
    }

    public static function configureMailer(): string
    {
        if (app()->environment('testing')) return config('mail.default');
        $settings = static::current();
        config(['mail.mailers.seller_smtp' => [
            'transport' => 'smtp', 'scheme' => $settings->encryption === 'ssl' ? 'smtps' : 'smtp',
            'host' => $settings->host, 'port' => (int) $settings->port,
            'username' => $settings->username,
            'password' => $settings->password ?? config('mail.mailers.smtp.password'),
            'require_tls' => true, 'timeout' => 15,
        ]]);
        app('mail.manager')->purge('seller_smtp');
        return 'seller_smtp';
    }
}
