<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use App\Models\User;

/**
 * TestEmail Command
 *
 * Artisan command to test email configuration.
 * Sends a test welcome email to verify SMTP settings.
 * Usage: php artisan email:test {email}
 */
class TestEmail extends Command
{
    protected $signature = 'email:test {email}';
    protected $description = 'Test email sending functionality';

    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Testing email to: {$email}");
        
        try {
            // Create a dummy user for testing
            $testUser = new User([
                'name' => 'Test User',
                'email' => $email,
            ]);
            
            Mail::to($email)->send(new WelcomeMail($testUser));
            
            $this->info("✅ Email sent successfully!");
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to send email: " . $e->getMessage());
            $this->error("Check your .env mail configuration");
        }
    }
}