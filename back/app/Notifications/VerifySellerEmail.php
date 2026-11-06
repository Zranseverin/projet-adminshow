<?php

namespace App\Notifications;

use App\Models\MailSetting;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifySellerEmail extends VerifyEmail
{
    public function toMail($notifiable)
    {
        $settings = MailSetting::forRecipient($notifiable);
        $verificationUrl = $this->verificationUrl($notifiable);
        return (new MailMessage)->mailer(MailSetting::configureMailer($settings))
            ->from($settings->from_address, $settings->from_name)
            ->subject('Validez votre adresse e-mail · Zran Market')
            ->greeting('Bienvenue '.$notifiable->name.' !')
            ->line('Validez votre adresse e-mail pour accéder à votre boutique Zran Market.')
            ->action('Valider mon adresse e-mail', $verificationUrl)
            ->line('Ce lien expire dans 60 minutes. Si vous n’avez pas créé ce compte, ignorez ce message.')
            ->salutation('L’équipe Zran Market')
            ->view(['html' => 'emails.verify-seller', 'text' => 'emails.verify-seller-text'], [
                'sellerName' => $notifiable->name,
                'verificationUrl' => $verificationUrl,
                'expiresIn' => config('auth.verification.expire', 60),
                'brandUrl' => url('/'),
            ]);
    }
}
