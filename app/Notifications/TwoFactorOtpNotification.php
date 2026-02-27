<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorOtpNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $otp,
        private readonly int $expiresInMinutes
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Recruitment Portal OTP Verification')
            ->line('Use the following OTP to complete your login.')
            ->line('OTP: ' . $this->otp)
            ->line('This OTP will expire in ' . $this->expiresInMinutes . ' minutes.')
            ->line('If you did not request this login, please reset your password immediately.');
    }
}
