<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SimpleMailService;

class TestSimpleMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-simple {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test simple mail service using PHP mail() function';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Please provide a valid email address.');
            return 1;
        }

        $mailService = new SimpleMailService();
        
        $this->info('Testing simple mail service...');
        
        try {
            $result = $mailService->sendEmail(
                $email,
                'Test Email from Shopybook - Simple Mail Service',
                'This is a test email sent using PHP mail() function. If you receive this, the simple mail service is working!',
                'support@shopybook.com'
            );
            
            if ($result) {
                $this->info('✅ SUCCESS: Simple mail service is working!');
                $this->info('Check your email for the test message.');
            } else {
                $this->error('❌ FAILED: Simple mail service failed.');
            }
            
            return $result ? 0 : 1;
        } catch (\Exception $e) {
            $this->error('❌ ERROR: ' . $e->getMessage());
            return 1;
        }
    }
}
