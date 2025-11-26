<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Business;
use App\Models\User;

class NotifyMissingImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:notify-missing 
                            {--send : Actually send emails (default is preview only)}
                            {--email= : Send test email to specific address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notifications to users with missing business logos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $send = $this->option('send');
        $testEmail = $this->option('email');

        $this->info('📧 Missing Images Email Notification System');
        $this->newLine();

        // Get businesses with missing logos (NULL or empty)
        $affectedBusinesses = Business::whereNull('logo_path')
                                      ->orWhere('logo_path', '')
                                      ->with('user')
                                      ->get();

        if ($affectedBusinesses->isEmpty()) {
            $this->info('✅ No businesses with missing logos found!');
            return Command::SUCCESS;
        }

        $this->info("Found {$affectedBusinesses->count()} businesses with missing logos");
        $this->newLine();

        // Test email mode
        if ($testEmail) {
            $this->info("🧪 Test Mode: Sending sample email to {$testEmail}");
            $this->sendTestEmail($testEmail, $affectedBusinesses->first());
            return Command::SUCCESS;
        }

        // Preview mode (default)
        if (!$send) {
            $this->warn('⚠️  PREVIEW MODE - No emails will be sent');
            $this->info('Run with --send to actually send emails');
            $this->newLine();

            $this->table(
                ['Business ID', 'Business Name', 'Owner Email', 'Owner Phone', 'Status'],
                $affectedBusinesses->map(function ($business) {
                    return [
                        $business->id,
                        $business->name,
                        $business->user->email ?? $business->email ?? 'N/A',
                        $business->phone,
                        'Logo Missing',
                    ];
                })->toArray()
            );

            $this->newLine();
            $this->info("📧 {$affectedBusinesses->count()} emails would be sent");
            $this->info("Run: php artisan images:notify-missing --send");
            
            return Command::SUCCESS;
        }

        // Actually send emails
        $this->info('📨 Sending emails to affected business owners...');
        $this->newLine();

        $sent = 0;
        $failed = 0;

        $progressBar = $this->output->createProgressBar($affectedBusinesses->count());
        $progressBar->start();

        foreach ($affectedBusinesses as $business) {
            try {
                $this->sendNotificationEmail($business);
                $sent++;
                $progressBar->advance();
            } catch (\Exception $e) {
                $failed++;
                $this->error("\n✗ Failed to send to {$business->name}: " . $e->getMessage());
                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info('=== Email Summary ===');
        $this->table(
            ['Status', 'Count'],
            [
                ['✅ Sent Successfully', $sent],
                ['✗ Failed', $failed],
                ['Total Attempted', $affectedBusinesses->count()],
            ]
        );

        if ($sent > 0) {
            $this->info("✅ Successfully sent {$sent} notification emails!");
        }

        if ($failed > 0) {
            $this->warn("⚠️  {$failed} emails failed to send. Check logs for details.");
        }

        return Command::SUCCESS;
    }

    /**
     * Send notification email to business owner
     */
    private function sendNotificationEmail(Business $business)
    {
        $recipientEmail = $business->user->email ?? $business->email;

        if (!$recipientEmail) {
            throw new \Exception('No email address available');
        }

        $data = [
            'businessName' => $business->name,
            'ownerName' => $business->user->name ?? 'Business Owner',
            'editUrl' => route('business.edit'),
            'supportEmail' => config('mail.from.address'),
        ];

        Mail::send('emails.missing-logo-notification', $data, function ($message) use ($recipientEmail, $business) {
            $message->to($recipientEmail)
                    ->subject('Action Required: Re-upload Your Business Logo - ' . $business->name);
        });
    }

    /**
     * Send test email
     */
    private function sendTestEmail(string $email, ?Business $sampleBusiness)
    {
        $data = [
            'businessName' => $sampleBusiness->name ?? 'Sample Business',
            'ownerName' => 'Test User',
            'editUrl' => route('business.edit'),
            'supportEmail' => config('mail.from.address'),
        ];

        try {
            Mail::send('emails.missing-logo-notification', $data, function ($message) use ($email) {
                $message->to($email)
                        ->subject('[TEST] Action Required: Re-upload Your Business Logo');
            });

            $this->info("✅ Test email sent to {$email}");
        } catch (\Exception $e) {
            $this->error("✗ Failed to send test email: " . $e->getMessage());
        }
    }
}

