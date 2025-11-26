<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Re-upload Your Business Logo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #020258, #13e8e9);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px 20px;
        }
        .alert {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .steps {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .steps h3 {
            color: #020258;
            margin-top: 0;
        }
        .steps ol {
            margin: 10px 0;
            padding-left: 20px;
        }
        .steps li {
            margin: 10px 0;
        }
        .button {
            display: inline-block;
            background: #13e8e9;
            color: #020258;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .button:hover {
            background: #020258;
            color: #13e8e9;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
        }
        .icon {
            font-size: 48px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏢 Shopybook</h1>
            <p>Business Management Platform</p>
        </div>

        <div class="content">
            <div class="icon" style="text-align: center;">⚠️</div>
            
            <h2 style="color: #020258; text-align: center;">Action Required: Re-upload Your Business Logo</h2>
            
            <p>Dear {{ $ownerName }},</p>

            <p>We're writing to inform you that your business logo for <strong>{{ $businessName }}</strong> needs to be re-uploaded.</p>

            <div class="alert">
                <strong>What happened?</strong><br>
                Due to recent system maintenance, some uploaded images were affected. We apologize for this inconvenience.
            </div>

            <div class="steps">
                <h3>How to Re-upload Your Logo:</h3>
                <ol>
                    <li>Log in to your Shopybook dashboard</li>
                    <li>Navigate to <strong>Business Settings</strong></li>
                    <li>Click <strong>Edit Business</strong></li>
                    <li>Upload your business logo again</li>
                    <li>Save your changes</li>
                </ol>
            </div>

            <div style="text-align: center;">
                <a href="{{ $editUrl }}" class="button">Upload Logo Now →</a>
            </div>

            <p><strong>Important:</strong></p>
            <ul>
                <li>Your account and all business data are safe</li>
                <li>Only the logo file needs to be re-uploaded</li>
                <li>Supported formats: JPG, PNG, SVG (Max 2MB)</li>
                <li>This is a one-time action</li>
            </ul>

            <p>If you don't have your original logo file, you can:</p>
            <ul>
                <li>Contact your graphic designer</li>
                <li>Use a temporary placeholder</li>
                <li>Contact our support team for assistance</li>
            </ul>

            <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">

            <p><strong>Need Help?</strong></p>
            <p>If you have any questions or need assistance, please contact us:</p>
            <ul>
                <li>📧 Email: <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></li>
                <li>📱 WhatsApp: +254 717745891</li>
                <li>⏰ Mon-Fri, 8AM-6PM EAT</li>
            </ul>

            <p>We apologize for any inconvenience this may cause and appreciate your understanding.</p>

            <p>Best regards,<br>
            <strong>The Shopybook Team</strong></p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} Shopybook. All rights reserved.</p>
            <p>You received this email because your business is registered on Shopybook.</p>
            <p>
                <a href="{{ config('app.url') }}" style="color: #666;">Visit Website</a> | 
                <a href="mailto:{{ $supportEmail }}" style="color: #666;">Contact Support</a>
            </p>
        </div>
    </div>
</body>
</html>

