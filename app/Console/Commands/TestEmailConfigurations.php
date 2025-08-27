<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class TestEmailConfigurations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-config {email} {--config=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test different email configurations to troubleshoot SMTP issues';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        $config = $this->option('config');
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Please provide a valid email address.');
            return 1;
        }

        $configurations = [
            'default' => [
                'host' => 'mail.shopybook.com',
                'port' => 465,
                'encryption' => 'ssl',
                'username' => 'support@shopybook.com',
                'password' => 'hgX$TvE~sr}&Miuq',
            ],
            'tls-587' => [
                'host' => 'mail.shopybook.com',
                'port' => 587,
                'encryption' => 'tls',
                'username' => 'support@shopybook.com',
                'password' => 'hgX$TvE~sr}&Miuq',
            ],
            'ssl-587' => [
                'host' => 'mail.shopybook.com',
                'port' => 587,
                'encryption' => 'ssl',
                'username' => 'support@shopybook.com',
                'password' => 'hgX$TvE~sr}&Miuq',
            ],
            'quoted-password' => [
                'host' => 'mail.shopybook.com',
                'port' => 465,
                'encryption' => 'ssl',
                'username' => 'support@shopybook.com',
                'password' => '"hgX$TvE~sr}&Miuq"',
            ],
        ];

        if ($config === 'all') {
            foreach ($configurations as $name => $settings) {
                $this->info("\nTesting configuration: {$name}");
                $this->testConfiguration($email, $settings, $name);
            }
        } else {
            $settings = $configurations[$config] ?? $configurations['default'];
            $this->testConfiguration($email, $settings, $config);
        }

        return 0;
    }

    private function testConfiguration($email, $settings, $configName)
    {
        try {
            // Temporarily update mail configuration
            Config::set('mail.mailers.smtp.host', $settings['host']);
            Config::set('mail.mailers.smtp.port', $settings['port']);
            Config::set('mail.mailers.smtp.encryption', $settings['encryption']);
            Config::set('mail.mailers.smtp.username', $settings['username']);
            Config::set('mail.mailers.smtp.password', $settings['password']);

            Mail::raw("Test email from Shopybook using configuration: {$configName}", function ($message) use ($email, $configName) {
                $message->to($email)
                        ->subject("Shopybook Email Test - {$configName}")
                        ->from('support@shopybook.com', 'Shopybook');
            });

            $this->info("✅ SUCCESS: Configuration '{$configName}' works!");
            $this->info("   Host: {$settings['host']}:{$settings['port']} ({$settings['encryption']})");
            return true;
        } catch (\Exception $e) {
            $this->error("❌ FAILED: Configuration '{$configName}' failed!");
            $this->error("   Error: " . $e->getMessage());
            $this->error("   Host: {$settings['host']}:{$settings['port']} ({$settings['encryption']})");
            return false;
        }
    }
}
