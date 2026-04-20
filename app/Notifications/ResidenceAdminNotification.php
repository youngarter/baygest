<?php

namespace App\Notifications;

use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResidenceAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $body,
        public ?string $actionUrl = null,
        public ?string $actionLabel = null,
    ) {
        $this->afterCommit();
    }

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /** @return array<string, mixed> */
    public function toDatabase(object $notifiable): array
    {
        $notification = FilamentNotification::make()
            ->title($this->title)
            ->body($this->body)
            ->info();

        if ($this->actionUrl && $this->actionLabel) {
            $notification->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label($this->actionLabel)
                    ->url($this->actionUrl),
            ]);
        }

        return $notification->getDatabaseMessage();
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->line($this->body);

        if ($this->actionUrl && $this->actionLabel) {
            $mail->action($this->actionLabel, $this->actionUrl);
        }

        return $mail;
    }
}
