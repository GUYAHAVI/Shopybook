<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Customer Review</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa; }
        .email-container { background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #ff511a; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #7b2e2e; margin: 0; font-size: 24px; }
        .business-name { color: #ff511a; font-size: 18px; margin-top: 5px; }
        .review-box { background: #f0fbfb; border-left: 4px solid #ff511a; border-radius: 4px; padding: 20px; margin: 20px 0; }
        .reviewer-name { font-weight: bold; font-size: 16px; color: #7b2e2e; margin-bottom: 4px; }
        .reviewer-role { color: #888; font-size: 13px; margin-bottom: 12px; }
        .stars { color: #f59e0b; font-size: 20px; letter-spacing: 2px; margin-bottom: 12px; }
        .quote { color: #444; font-style: italic; font-size: 15px; }
        .action-section { text-align: center; margin: 30px 0; }
        .action-section p { color: #666; margin-bottom: 16px; font-size: 14px; }
        .btn { display: inline-block; padding: 12px 28px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 15px; margin: 0 8px; }
        .btn-approve { background: #ff511a; color: #7b2e2e; }
        .btn-reject  { background: #f1f1f1; color: #555; border: 1px solid #ccc; }
        .btn-manage  { background: #7b2e2e; color: white; margin-top: 12px; display: inline-block; }
        .footer { text-align: center; font-size: 12px; color: #aaa; margin-top: 30px; border-top: 1px solid #eee; padding-top: 16px; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>⭐ New Customer Review</h1>
            <div class="business-name">{{ $business->name }}</div>
        </div>

        <p>A visitor just left a review on your Shopybook website. Review it below and decide whether to publish it.</p>

        <div class="review-box">
            <div class="reviewer-name">{{ $testimonial->name }}</div>
            @if($testimonial->role)
                <div class="reviewer-role">{{ $testimonial->role }}</div>
            @endif
            <div class="stars">
                @for($i = 1; $i <= 5; $i++)
                    {{ $i <= $testimonial->rating ? '★' : '☆' }}
                @endfor
                <span style="color:#888; font-size:13px; margin-left:6px;">{{ $testimonial->rating }}/5</span>
            </div>
            <div class="quote">"{{ $testimonial->quote }}"</div>
        </div>

        <div class="action-section">
            <p>Approve to show this review on your website, or reject to hide it.</p>
            <a href="{{ $approveUrl }}" class="btn btn-approve">✓ Approve Review</a>
            <a href="{{ $rejectUrl }}" class="btn btn-reject">✗ Reject Review</a>
            <br>
            <a href="{{ $manageUrl }}" class="btn btn-manage" style="margin-top:16px;">Manage All Reviews →</a>
        </div>

        <p style="font-size:13px; color:#888;">
            <strong>Note:</strong> Clicking Approve or Reject will require you to be logged in to Shopybook.
            You can also manage all reviews from your dashboard at any time.
        </p>

        <div class="footer">
            <p>This notification was sent by Shopybook because a customer submitted a review on your website.<br>
            © {{ date('Y') }} Shopybook. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
