<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestMailServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test connectivity to mail server';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Testing connectivity to mail.shopybook.com...');
        
        // Test basic connectivity
        $this->testBasicConnectivity();
        
        // Test different ports
        $this->testPorts();
        
        // Test DNS resolution
        $this->testDNS();
        
        return 0;
    }

    private function testBasicConnectivity()
    {
        $this->info("\n1. Testing basic connectivity...");
        
        $host = 'mail.shopybook.com';
        $ports = [465, 587, 25, 110, 143];
        
        foreach ($ports as $port) {
            $connection = @fsockopen($host, $port, $errno, $errstr, 10);
            
            if ($connection) {
                $this->info("   ✅ Port {$port}: OPEN");
                fclose($connection);
            } else {
                $this->error("   ❌ Port {$port}: CLOSED ({$errstr})");
            }
        }
    }

    private function testPorts()
    {
        $this->info("\n2. Testing SMTP ports with timeout...");
        
        $host = 'mail.shopybook.com';
        $ports = [
            ['port' => 465, 'type' => 'SSL'],
            ['port' => 587, 'type' => 'TLS'],
            ['port' => 25, 'type' => 'Plain'],
        ];
        
        foreach ($ports as $test) {
            $this->info("   Testing {$test['type']} on port {$test['port']}...");
            
            $connection = @fsockopen($host, $test['port'], $errno, $errstr, 15);
            
            if ($connection) {
                $this->info("   ✅ {$test['type']} port {$test['port']}: SUCCESS");
                
                // Try to read SMTP banner
                $banner = fgets($connection, 1024);
                if ($banner) {
                    $this->info("   📧 SMTP Banner: " . trim($banner));
                }
                
                fclose($connection);
            } else {
                $this->error("   ❌ {$test['type']} port {$test['port']}: FAILED ({$errstr})");
            }
        }
    }

    private function testDNS()
    {
        $this->info("\n3. Testing DNS resolution...");
        
        $host = 'mail.shopybook.com';
        
        $ip = gethostbyname($host);
        
        if ($ip !== $host) {
            $this->info("   ✅ DNS Resolution: {$host} -> {$ip}");
            
            // Test reverse DNS
            $reverse = gethostbyaddr($ip);
            $this->info("   🔄 Reverse DNS: {$ip} -> {$reverse}");
        } else {
            $this->error("   ❌ DNS Resolution failed for {$host}");
        }
    }
}
