<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #334155; }
        .container { max-width: 600px; margin: 0 auto; padding: 40px; border: 1px solid #e2e8f0; border-radius: 16px; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #1e293b; text-transform: uppercase; letter-spacing: 2px; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; background: #ecfdf5; color: #059669; font-size: 10px; font-weight: bold; text-transform: uppercase; margin-bottom: 20px; }
        .details { background: #f8fafc; padding: 20px; border-radius: 12px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; }
        .detail-label { color: #64748b; font-weight: bold; }
        .detail-value { color: #1e293b; font-weight: bold; }
        .button { display: block; text-align: center; background: #1e293b; color: #ffffff; padding: 14px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-top: 30px; }
        .footer { text-align: center; margin-top: 40px; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">HAALAND LOGISTICS</div>
        </div>
        
        <div style="text-align: center;">
            <div class="status-badge">Booking Confirmed</div>
            <h1 style="font-size: 20px; margin-bottom: 10px;">Your shipment is scheduled!</h1>
            <p style="font-size: 14px; color: #64748b;">Reference Number: <strong>{{ $booking->booking_number }}</strong></p>
        </div>

        <div class="details">
            <div class="detail-row">
                <span class="detail-label">Drop-off Date:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($booking->drop_off_date)->format('M d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Drop-off Time:</span>
                <span class="detail-value">{{ $booking->drop_off_time }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Origin:</span>
                <span class="detail-value">{{ $booking->quote->origin->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Volume:</span>
                <span class="detail-value">{{ number_format($booking->quote->volume_cft, 1) }} CFT</span>
            </div>
        </div>

        @if($booking->is_special_request)
            <p style="font-size: 12px; color: #b45309; background: #fffbeb; padding: 10px; border-radius: 8px; border: 1px solid #fef3c7;">
                <strong>Note:</strong> This booking is marked as a special request/off-hours delivery. Our operations team will contact you to finalize the entry.
            </p>
        @endif

        <a href="{{ route('bookings.index') }}" class="button">View Booking Details</a>

        <div class="footer">
            &copy; {{ date('Y') }} Haaland Logistics. All rights reserved.<br>
            Secure B2B Consolidation Portal
        </div>
    </div>
</body>
</html>
