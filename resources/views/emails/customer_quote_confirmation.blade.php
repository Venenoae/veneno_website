<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quote Request Received — Veneno Auto Care Center</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #09090b;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      color: #f4f4f5;
      -webkit-font-smoothing: antialiased;
    }
    .wrapper {
      width: 100%;
      background-color: #09090b;
      padding: 30px 15px;
      box-sizing: border-box;
    }
    .container {
      max-width: 600px;
      margin: 0 auto;
      background-color: #121215;
      border: 1px solid #27272a;
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 20px 40px rgba(0,0,0,0.6);
    }
    .header {
      background: linear-gradient(135deg, #18181b 0%, #09090b 100%);
      padding: 32px 24px;
      text-align: center;
      border-bottom: 2px solid #dc2626;
    }
    .brand {
      font-size: 20px;
      font-weight: 900;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: #ffffff;
      margin-bottom: 8px;
    }
    .badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 20px;
      background-color: rgba(220, 38, 38, 0.2);
      border: 1px solid rgba(220, 38, 38, 0.4);
      color: #f87171;
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      margin-bottom: 12px;
    }
    .title {
      margin: 0;
      font-size: 22px;
      font-weight: 800;
      color: #ffffff;
    }
    .body-content {
      padding: 28px 24px;
      line-height: 1.6;
      font-size: 14px;
      color: #d4d4d8;
    }
    .greeting {
      font-size: 16px;
      font-weight: 700;
      color: #ffffff;
      margin-bottom: 12px;
    }
    .card {
      background-color: #18181b;
      border: 1px solid #27272a;
      border-radius: 12px;
      padding: 16px 20px;
      margin: 20px 0;
    }
    .card-row {
      display: flex;
      justify-content: space-between;
      padding: 6px 0;
      border-bottom: 1px solid #27272a;
      font-size: 13px;
    }
    .card-row:last-child {
      border-bottom: none;
    }
    .card-label {
      color: #a1a1aa;
    }
    .card-val {
      color: #ffffff;
      font-weight: 700;
    }
    .btn {
      display: block;
      width: 100%;
      max-width: 320px;
      margin: 24px auto 10px auto;
      padding: 14px 20px;
      border-radius: 12px;
      font-size: 13px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      text-decoration: none;
      text-align: center;
      background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
      color: #ffffff !important;
      box-shadow: 0 10px 20px rgba(220, 38, 38, 0.3);
    }
    .footer {
      padding: 20px 24px;
      background-color: #09090b;
      text-align: center;
      border-top: 1px solid #27272a;
      font-size: 11px;
      color: #71717a;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="container">
      <div class="header">
        <div class="brand">VENENO AUTO CARE CENTER</div>
        <div class="badge">Official 3M & GYEON Certified Studio</div>
        <h1 class="title">Thank You For Reaching Out</h1>
      </div>

      <div class="body-content">
        <div class="greeting">Dear {{ $lead['name'] ?? 'Valued Client' }},</div>
        <p>
          We have successfully received your quote request for <strong>{{ $lead['service'] ?? 'Auto Care Service' }}</strong>. 
          Our concierge team is reviewing your vehicle requirements and will contact you via WhatsApp or phone shortly.
        </p>

        <div class="card">
          <table style="width: 100%; border-collapse: collapse;">
            <tr>
              <td style="padding: 6px 0; color: #a1a1aa; font-size: 13px;">Reference #:</td>
              <td style="padding: 6px 0; color: #f87171; font-weight: 700; font-size: 13px; text-align: right;">#{{ $inquiry->id }}</td>
            </tr>
            <tr>
              <td style="padding: 6px 0; color: #a1a1aa; font-size: 13px;">Requested Service:</td>
              <td style="padding: 6px 0; color: #ffffff; font-weight: 700; font-size: 13px; text-align: right;">{{ $lead['service'] ?? 'General Care' }}</td>
            </tr>
            <tr>
              <td style="padding: 6px 0; color: #a1a1aa; font-size: 13px;">Preferred Studio:</td>
              <td style="padding: 6px 0; color: #ffffff; font-weight: 700; font-size: 13px; text-align: right;">{{ $lead['branch'] ?? 'Musaffah Main Facility' }}</td>
            </tr>
          </table>
        </div>

        <p style="font-size: 13px; color: #a1a1aa;">
          Need immediate assistance or wish to send photos of your vehicle? Connect with our master detailers directly on WhatsApp:
        </p>

        <a href="https://wa.me/97126344403?text={{ urlencode('Hello Veneno Concierge, I have submitted Quote Request #' . $inquiry->id . ' for ' . ($lead['service'] ?? 'services') . '.') }}" class="btn" target="_blank">
          Chat With Concierge on WhatsApp
        </a>
      </div>

      <div class="footer">
        <p style="margin: 0 0 6px 0;"><strong>Veneno Auto Care Center</strong> — Abu Dhabi, United Arab Emirates</p>
        <p style="margin: 0;">Hotline: +971 2 634 4403 • <a href="https://veneno.ae" style="color: #dc2626; text-decoration: none;">veneno.ae</a></p>
      </div>
    </div>
  </div>
</body>
</html>
