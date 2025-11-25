<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    protected $signature = 'test:email {email}';
    protected $description = 'Test sending email';

    public function handle()
    {
        $email = $this->argument('email');

        try {
            Mail::raw('Test email from NGUNDUR - Email verification system is working! 🌾', function ($message) use ($email) {
                $message->to($email)
                    ->subject('Test Email - NGUNDUR');
            });

            $this->info("✅ Email sent successfully to {$email}!");
            $this->info("Check your inbox (and spam folder).");

        } catch (\Exception $e) {
            $this->error("❌ Failed to send email!");
            $this->error("Error: " . $e->getMessage());

            // Show config untuk debug
            $this->warn("\nCurrent Mail Config:");
            $this->line("MAIL_MAILER: " . config('mail.default'));
            $this->line("MAIL_HOST: " . config('mail.mailers.smtp.host'));
            $this->line("MAIL_PORT: " . config('mail.mailers.smtp.port'));
            $this->line("MAIL_USERNAME: " . config('mail.mailers.smtp.username'));
            $this->line("MAIL_FROM: " . config('mail.from.address'));
        }
    }
}
