<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #7b2e2e;
            color: #ff511a;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 0 0 8px 8px;
        }
        .field {
            margin-bottom: 20px;
            padding: 15px;
            background: white;
            border-left: 4px solid #ff511a;
            border-radius: 4px;
        }
        .field-label {
            font-weight: bold;
            color: #7b2e2e;
            margin-bottom: 5px;
            display: block;
        }
        .field-value {
            color: #333;
            font-size: 15px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ff511a;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            background: #ff511a;
            color: #7b2e2e;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">📧 New Contact Form Submission</h1>
        <p style="margin: 10px 0 0 0; color: #ff511a;">Someone is interested in Shopybook!</p>
    </div>
    
    <div class="content">
        <div class="field">
            <span class="field-label">👤 Name:</span>
            <span class="field-value">{{ $data['name'] }}</span>
        </div>

        <div class="field">
            <span class="field-label">📧 Email:</span>
            <span class="field-value">
                <a href="mailto:{{ $data['email'] }}" style="color: #7b2e2e; text-decoration: none;">
                    {{ $data['email'] }}
                </a>
            </span>
        </div>

        <div class="field">
            <span class="field-label">📱 Phone:</span>
            <span class="field-value">
                <a href="tel:{{ $data['phone'] }}" style="color: #7b2e2e; text-decoration: none;">
                    {{ $data['phone'] }}
                </a>
            </span>
        </div>

        <div class="field">
            <span class="field-label">🏢 Business Type:</span>
            <span class="field-value">{{ ucfirst($data['business_type']) }}</span>
            <span class="badge">{{ ucfirst($data['business_type']) }}</span>
        </div>

        <div class="field">
            <span class="field-label">💬 Message:</span>
            <div class="field-value" style="margin-top: 10px; white-space: pre-wrap;">{{ $data['message'] }}</div>
        </div>

        <div style="margin-top: 30px; padding: 15px; background: #fffbea; border-left: 4px solid #ffc107; border-radius: 4px;">
            <p style="margin: 0; color: #856404;">
                <strong>⚡ Quick Actions:</strong><br>
                Reply directly to this email or call {{ $data['phone'] }} to follow up with this lead.
            </p>
        </div>
    </div>

    <div class="footer">
        <p>This email was sent from the Shopybook contact form on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p style="margin-top: 10px;">
            <strong>Shopybook</strong> - Transform Your Business<br>
            <a href="https://shopybook.com" style="color: #ff511a; text-decoration: none;">www.shopybook.com</a>
        </p>
    </div>
</body>
</html>
