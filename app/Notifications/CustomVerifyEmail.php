<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmailBase
{
    // Removed ShouldQueue - email akan langsung dikirim tanpa queue

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verifikasi Email Anda - NGUNDUR')
            ->greeting('Halo, ' . ($notifiable->nama ?? $notifiable->username) . '! 👋')
            ->line('Selamat datang di **NGUNDUR** - Platform jual beli produk pertanian berkualitas.')
            ->line('Terima kasih telah mendaftar! Untuk melanjutkan, mohon verifikasi alamat email Anda dengan mengklik tombol di bawah ini:')
            ->action('Verifikasi Email Saya', $verificationUrl)
            ->line('Link verifikasi ini akan kedaluwarsa dalam **60 menit**.')
            ->line('')
            ->line('Jika Anda tidak membuat akun di NGUNDUR, abaikan email ini.')
            ->line('')
            ->line('Salam hangat,')
            ->salutation('**Tim NGUNDUR** 🌾');
    }
}
