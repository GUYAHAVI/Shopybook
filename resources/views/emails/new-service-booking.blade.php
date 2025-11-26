<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Service Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #13e8e9;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #020258;
            margin: 0;
            font-size: 24px;
        }
        .business-name {
            color: #13e8e9;
            font-size: 18px;
            margin-top: 5px;
        }
        .booking-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .booking-info h3 {
            color: #020258;
            margin-top: 0;
            border-bottom: 1px solid #13e8e9;
            padding-bottom: 10px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #020258;
        }
        .value {
            color: #666;
        }
        .services-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .services-table th,
        .services-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .services-table th {
            background-color: #13e8e9;
            color: #020258;
            font-weight: bold;
        }
        .total-amount {
            background: #020258;
            color: white;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            margin: 20px 0;
        }
        .total-amount h3 {
            margin: 0;
            font-size: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .cta-button {
            display: inline-block;
            background: #13e8e9;
            color: #020258;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🎉 New Service Booking!</h1>
            <div class="business-name">{{ $business->business_name }}</div>
        </div>

        <div class="booking-info">
            <h3>📋 Booking Details</h3>
            <div class="info-row">
                <span class="label">Booking ID:</span>
                <span class="value">#{{ $serviceBooking->id }}</span>
            </div>
            <div class="info-row">
                <span class="label">Date & Time:</span>
                <span class="value">{{ $serviceBooking->service_date->format('M d, Y - g:i A') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Customer:</span>
                <span class="value">{{ $customer ? $customer->name : 'Walk-in Customer' }}</span>
            </div>
            @if($customer && $customer->phone)
            <div class="info-row">
                <span class="label">Phone:</span>
                <span class="value">{{ $customer->phone }}</span>
            </div>
            @endif
            @if($customer && $customer->email)
            <div class="info-row">
                <span class="label">Email:</span>
                <span class="value">{{ $customer->email }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="label">Payment Method:</span>
                <span class="value">{{ ucfirst($serviceBooking->payment_method) }}</span>
            </div>
            <div class="info-row">
                <span class="label">Payment Status:</span>
                <span class="value">{{ ucfirst($serviceBooking->payment_status) }}</span>
            </div>
        </div>

        <h3>💼 Services Booked</h3>
        <table class="services-table">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Staff Member</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($serviceItems as $item)
                <tr>
                    <td>{{ $item->service->name ?? 'Service' }}</td>
                    <td>{{ $item->staff->name ?? 'Not Assigned' }}</td>
                    <td>KSh {{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-amount">
            <h3>Total Amount: KSh {{ number_format($serviceBooking->final_amount, 2) }}</h3>
            @if($serviceBooking->discount_amount > 0)
                <small>Subtotal: KSh {{ number_format($serviceBooking->subtotal, 2) }} | Discount: KSh {{ number_format($serviceBooking->discount_amount, 2) }}</small>
            @endif
        </div>

        @if($serviceBooking->notes)
        <div class="booking-info">
            <h3>📝 Notes</h3>
            <p>{{ $serviceBooking->notes }}</p>
        </div>
        @endif

        <div style="text-align: center;">
            <a href="{{ url('/service-bookings/' . $serviceBooking->id) }}" class="cta-button">
                View Booking Details
            </a>
        </div>

        <div class="footer">
            <p>This is an automated notification from {{ config('app.name') }}.</p>
            <p>Please log in to your dashboard to manage this booking.</p>
        </div>
    </div>
</body>
</html>

