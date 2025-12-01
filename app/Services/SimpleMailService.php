<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SimpleMailService
{
    public function sendEmail($to, $subject, $message, $from = null)
    {
        try {
            $from = $from ?: 'support@shopybook.com';
            
            // Set headers
            $headers = [
                'From: ' . $from,
                'Reply-To: ' . $from,
                'X-Mailer: PHP/' . phpversion(),
                'Content-Type: text/html; charset=UTF-8',
                'MIME-Version: 1.0'
            ];
            
            // Send email using PHP's mail() function
            $result = mail($to, $subject, $message, implode("\r\n", $headers));
            
            if ($result) {
                Log::info('Email sent via PHP mail()', [
                    'to' => $to,
                    'subject' => $subject,
                    'from' => $from
                ]);
                return true;
            } else {
                Log::error('PHP mail() failed', [
                    'to' => $to,
                    'subject' => $subject,
                    'from' => $from
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Simple mail service error', [
                'error' => $e->getMessage(),
                'to' => $to,
                'subject' => $subject
            ]);
            return false;
        }
    }

    public function sendVerificationEmail($user, $verificationUrl)
    {
        $subject = 'Verify Your Email Address - Shopybook';
        $message = $this->getVerificationEmailTemplate($user->name, $verificationUrl);
        
        return $this->sendEmail($user->email, $subject, $message);
    }

    public function sendPasswordResetEmail($user, $resetUrl)
    {
        $subject = 'Reset Password Notification - Shopybook';
        $message = $this->getPasswordResetEmailTemplate($user->name, $resetUrl);
        
        return $this->sendEmail($user->email, $subject, $message);
    }

    private function getVerificationEmailTemplate($name, $verificationUrl)
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Verify Your Email - Shopybook</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <div style='background-color: #4F46E5; color: white; padding: 20px; text-align: center;'>
                    <h1 style='margin: 0;'>Welcome to Shopybook!</h1>
                </div>
                
                <div style='background-color: #f8fafc; padding: 20px;'>
                    <p>Hello {$name},</p>
                    
                    <p>Thank you for registering with Shopybook. To complete your registration, please verify your email address by clicking the button below.</p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$verificationUrl}' 
                           style='background-color: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;'>
                            Verify Email Address
                        </a>
                    </div>
                    
                    <p>If you did not create an account, no further action is required.</p>
                    
                    <p>This verification link will expire in 60 minutes.</p>
                    
                    <p>Best regards,<br>The Shopybook Team</p>
                </div>
                
                <div style='background-color: #1f2937; color: white; padding: 20px; text-align: center; font-size: 14px;'>
                    <p>© 2024 Shopybook. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    private function getPasswordResetEmailTemplate($name, $resetUrl)
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Reset Password - Shopybook</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <div style='background-color: #4F46E5; color: white; padding: 20px; text-align: center;'>
                    <h1 style='margin: 0;'>Password Reset</h1>
                </div>
                
                <div style='background-color: #f8fafc; padding: 20px;'>
                    <p>Hello {$name},</p>
                    
                    <p>You are receiving this email because we received a password reset request for your account.</p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$resetUrl}' 
                           style='background-color: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;'>
                            Reset Password
                        </a>
                    </div>
                    
                    <p>This password reset link will expire in 60 minutes.</p>
                    
                    <p>If you did not request a password reset, no further action is required.</p>
                    
                    <p>Best regards,<br>The Shopybook Team</p>
                </div>
                
                <div style='background-color: #1f2937; color: white; padding: 20px; text-align: center; font-size: 14px;'>
                    <p>© 2024 Shopybook. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
