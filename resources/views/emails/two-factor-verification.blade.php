<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Verification Required</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #ff511a;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #7b2e2e;
            margin-bottom: 10px;
        }
        .verification-code {
            background-color: #f8f9fa;
            border: 2px solid #ff511a;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            font-size: 32px;
            font-weight: bold;
            color: #7b2e2e;
            letter-spacing: 5px;
        }
        .action-details {
            background-color: #e8f4f8;
            border-left: 4px solid #ff511a;
            padding: 15px;
            margin: 20px 0;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background-color: #7b2e2e;
            color: #ffffff;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Shopybook</div>
            <h2>Security Verification Required</h2>
        </div>

        <p>Hello <strong>{{ $user->name }}</strong>,</p>

        <p>We received a request to perform a sensitive action on your account. To ensure your security, please verify this action using the verification code below.</p>

        <div class="action-details">
            <strong>Action:</strong> {{ \App\Services\TwoFactorAuthService::getActionName($action) }}<br>
            <strong>Requested at:</strong> {{ now()->format('F j, Y \a\t g:i A') }}<br>
            <strong>IP Address:</strong> {{ request()->ip() ?? 'Unknown' }}
        </div>

        <div class="verification-code">
            {{ $code }}
        </div>

        <p><strong>This verification code will expire in 10 minutes.</strong></p>

        <div class="warning">
            <strong>⚠️ Security Notice:</strong><br>
            If you did not request this action, please ignore this email and consider changing your password immediately. This verification code can only be used once and will expire automatically.
        </div>

        <p>For your security, this code can only be used once and will expire automatically. If you need a new code, you can request one from your account settings.</p>

        <div class="footer">
            <p>This is an automated security message from Shopybook. Please do not reply to this email.</p>
            <p>If you have any questions, please contact our support team.</p>
            <p>&copy; {{ date('Y') }} Shopybook. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
