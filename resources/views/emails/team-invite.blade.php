<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 560px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        .header { background: #020258; color: #fff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; }
        .body { padding: 30px; }
        .creds { background: #f0f4ff; border: 1px solid #d0d8ff; border-radius: 6px; padding: 16px; margin: 20px 0; }
        .creds p { margin: 6px 0; font-size: 14px; }
        .creds strong { color: #020258; }
        .btn { display: inline-block; background: #13e8e9; color: #020258; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: bold; margin-top: 16px; }
        .footer { text-align: center; color: #999; font-size: 12px; padding: 20px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>You're invited to join {{ $business->name }}</h1>
    </div>
    <div class="body">
        <p>Hi {{ $user->name }},</p>
        <p>You have been invited to join <strong>{{ $business->name }}</strong> on Shopybook.</p>
        @if($tempPassword)
        <p>Your account has been created. Use the credentials below to log in:</p>
        <div class="creds">
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Temporary Password:</strong> {{ $tempPassword }}</p>
        </div>
        <p>Please change your password after logging in.</p>
        @else
        <p>Log in with your existing account to access the business dashboard.</p>
        @endif
        <a href="{{ $loginUrl }}" class="btn">Log in to Shopybook</a>
    </div>
    <div class="footer">
        This invitation was sent by the owner of {{ $business->name }}. If you did not expect this, please ignore this email.
    </div>
</div>
</body>
</html>
