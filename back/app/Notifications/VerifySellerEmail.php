<?php

namespace App\Notifications;

use App\Models\MailSetting;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifySellerEmail extends VerifyEmail
{
    public function toMail($notifiable)
    {
        $settings = MailSetting::current();
        return (new MailMessage)->mailer(MailSetting::configureMailer())
            ->from($settings->from_address, $settings->from_name)
            ->subject('Validez votre adresse e-mail · Zran Market')
            ->greeting('Bienvenue '.$notifiable->name.' !')
            ->line('Validez votre adresse e-mail pour accéder à votre boutique Zran Market.')
            ->action('Valider mon adresse e-mail', $this->verificationUrl($notifiable))
            ->line('Ce lien expire dans 60 minutes. Si vous n’avez pas créé ce compte, ignorez ce message.')
            ->salutation('L’équipe Zran Market');
    }
}
