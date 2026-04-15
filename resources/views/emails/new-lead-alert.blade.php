<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #334155; }
        .container { max-width: 600px; margin: 0 auto; padding: 40px; border: 1px solid #e2e8f0; border-radius: 16px; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #1e293b; text-transform: uppercase; letter-spacing: 2px; }
        .hot-lead { display: inline-block; padding: 4px 12px; border-radius: 9999px; background: #fef2f2; color: #dc2626; font-size: 10px; font-weight: bold; text-transform: uppercase; margin-bottom: 20px; }
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
            <div class="hot-lead">Action Required: New Inquiry</div>
            <h1 style="font-size: 20px; margin-bottom: 10px;">A new guest inquiry was received</h1>
            <p style="font-size: 14px; color: #64748b;">Contact Email: <strong>{{ $lead->email }}</strong></p>
        </div>

        <div class="details">
            <div class="detail-row">
                <span class="detail-label">Origin Warehouse:</span>
                <span class="detail-value">{{ $lead->origin->name ?? 'TBA' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Destination:</span>
                <span class="detail-value">{{ $lead->country->name ?? 'TBA' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Volume:</span>
                <span class="detail-value">{{ number_format($lead->volume_cft, 1) }} CFT</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">{{ strtoupper($lead->status) }}</span>
            </div>
        </div>

        <p style="font-size: 14px;">The user has been redirected to the registration page. Monitor the user panel for a new account conversion.</p>

        <a href="{{ url('/dashboard') }}" class="button">Open Admin Dashboard</a>

        <div class="footer">
            Strategic Command Center Alert System
        </div>
    </div>
</body>
</html>
