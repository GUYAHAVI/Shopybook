<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmail
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        // Try Laravel Mail first, fallback to simple mail service
        try {
            return (new MailMessage)
                ->subject('Verify Your Email Address - Shopybook')
                ->greeting('Welcome to Shopybook!')
                ->line('Thank you for registering with Shopybook. To complete your registration, please verify your email address by clicking the button below.')
                ->action('Verify Email Address', $verificationUrl)
                ->line('If you did not create an account, no further action is required.')
                ->line('This verification link will expire in 60 minutes.')
                ->salutation('Best regards, The Shopybook Team');
        } catch (\Exception $e) {
            // Fallback to simple mail service
            $simpleMailService = new \App\Services\SimpleMailService();
            $simpleMailService->sendVerificationEmail($notifiable, $verificationUrl);
            
            // Return a basic mail message for Laravel
            return (new MailMessage)
                ->subject('Verify Your Email Address - Shopybook')
                ->line('Email sent via fallback service.');
        }
    }

    /**
     * Get the verification email notification mail message for the given URL.
     *
     * @param  string  $url
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject('Verify Your Email Address - Shopybook')
            ->greeting('Welcome to Shopybook!')
            ->line('Thank you for registering with Shopybook. To complete your registration, please verify your email address by clicking the button below.')
            ->action('Verify Email Address', $url)
            ->line('If you did not create an account, no further action is required.')
            ->line('This verification link will expire in 60 minutes.')
            ->salutation('Best regards, The Shopybook Team');
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable);
        }

        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
