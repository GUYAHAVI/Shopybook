<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CpanelEmailService
{
    protected $cpanelUrl;
    protected $username;
    protected $password;
    protected $port;

    public function __construct()
    {
        $this->cpanelUrl = env('CPANEL_URL', 'https://shopybook.com:2083');
        $this->username = env('CPANEL_USERNAME');
        $this->password = env('CPANEL_PASSWORD');
        $this->port = env('CPANEL_PORT', 2083);
    }

    public function sendEmail($to, $subject, $message, $from = null)
    {
        try {
            $from = $from ?: 'support@shopybook.com';
            
            $response = Http::withBasicAuth($this->username, $this->password)
                ->post($this->cpanelUrl . '/execute/Email/send_email', [
                    'to' => $to,
                    'subject' => $subject,
                    'message' => $message,
                    'from' => $from,
                ]);

            if ($response->successful()) {
                Log::info('Email sent via cPanel API', [
                    'to' => $to,
                    'subject' => $subject,
                    'response' => $response->json()
                ]);
                return true;
            } else {
                Log::error('cPanel email API failed', [
                    'to' => $to,
                    'subject' => $subject,
                    'response' => $response->body(),
                    'status' => $response->status()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('cPanel email service error', [
                'error' => $e->getMessage(),
                'to' => $to,
                'subject' => $subject
            ]);
            return false;
        }
    }

    public function testConnection()
    {
        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->get($this->cpanelUrl . '/execute/Email/list_pops');

            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('cPanel connection test failed', [
                'error' => $e->getMessage(),
                'cpanel_url' => $this->cpanelUrl,
            ]);
            return false;
        }
    }
}
