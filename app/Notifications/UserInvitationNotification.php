<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $resetUrl) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bienvenue — Définissez votre mot de passe')
            ->greeting('Bonjour '.$notifiable->name)
            ->line('Vous avez été invité à rejoindre la plateforme Bayanv1.')
            ->line('Cliquez sur le lien ci-dessous pour définir votre mot de passe et accéder à votre compte.')
            ->action('Définir mon mot de passe', $this->resetUrl)
            ->line('Ce lien expire dans 60 minutes.')
            ->line('Merci,')
            ->salutation('L\'équipe Bayanv1');
    }
}
