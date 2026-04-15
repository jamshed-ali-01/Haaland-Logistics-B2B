<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #334155; }
        .container { max-width: 600px; margin: 0 auto; padding: 40px; border: 1px solid #e2e8f0; border-radius: 16px; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #1e293b; text-transform: uppercase; letter-spacing: 2px; }
        .quote-badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; background: #eff6ff; color: #2563eb; font-size: 10px; font-weight: bold; text-transform: uppercase; margin-bottom: 20px; }
        .price-box { text-align: center; background: #f1f5f9; padding: 30px; border-radius: 16px; margin: 20px 0; }
        .price-label { font-size: 12px; color: #64748b; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .price-value { font-size: 36px; font-weight: bold; color: #0f172a; margin: 10px 0; }
        .details { margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding: 12px 0; font-size: 14px; }
        .detail-label { color: #64748b; }
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
            <div class="quote-badge">Rate Quote Ready</div>
            <h1 style="font-size: 20px; margin-bottom: 10px;">Your shipping quote is here</h1>
            <p style="font-size: 14px; color: #64748b;">Reference: <strong>{{ $quote->reference_number }}</strong></p>
        </div>

        <div class="price-box">
            <div class="price-label">Total Estimated Investment</div>
            <div class="price-value">${{ number_format($quote->total_price, 2) }}</div>
            <div style="font-size: 11px; color: #94a3b8;">Includes origin fees and destination freight</div>
        </div>

        <div class="details">
            <div class="detail-row">
                <span class="detail-label">Origin:</span>
                <span class="detail-value">{{ $quote->origin->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Destination:</span>
                <span class="detail-value">{{ $quote->country->name }} ({{ $quote->region->name ?? 'Mainland' }})</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Volume:</span>
                <span class="detail-value">{{ number_format($quote->volume_cft, 1) }} CFT</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Service Type:</span>
                <span class="detail-value">{{ $quote->service_type }}</span>
            </div>
        </div>

        <a href="{{ route('bookings.create', $quote) }}" class="button">Convert to Booking Now</a>

        <div class="footer">
            &copy; {{ date('Y') }} Haaland Logistics. Rates valid for 14 days.
        </div>
    </div>
</body>
</html>
