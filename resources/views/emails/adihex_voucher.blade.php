<!DOCTYPE html>
<html lang="{{ $lead->locale ?? 'en' }}" dir="{{ ($lead->locale ?? 'en') === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ ($lead->locale ?? 'en') === 'ar' ? 'مركز فينينو للعناية بالسيارات — قسيمة الفائز في أديهيكس 2026' : 'Veneno Auto Care Center — ADIHEX 2026 Winner Voucher' }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #09090b; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #f4f4f5;">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #09090b; padding: 30px 10px;">
    <tr>
      <td align="center">
        <!-- Main Card Container -->
        <table width="100%" max-width="600" style="max-width: 600px; background-color: #18181b; border: 1px solid #27272a; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);">
          
          <!-- Header Banner with Gold & Red Luxury Accents -->
          <tr>
            <td align="center" style="padding: 30px 20px 20px; background: linear-gradient(180deg, #27272a 0%, #18181b 100%); border-bottom: 2px solid #c5a059;">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center">
                    <h1 style="margin: 0; font-size: 22px; font-weight: 900; color: #ef4444; letter-spacing: 2px; text-transform: uppercase;">
                      {{ ($lead->locale ?? 'en') === 'ar' ? 'مركز فينينو للعناية بالسيارات' : 'VENENO AUTO CARE CENTER' }}
                    </h1>
                    <p style="margin: 6px 0 0; font-size: 11px; font-weight: 700; color: #c5a059; letter-spacing: 1.5px; text-transform: uppercase;">
                      {{ ($lead->locale ?? 'en') === 'ar' ? 'قسيمة الفائز الرسمية — معرض أديهيكس 2026' : 'OFFICIAL ADIHEX 2026 WINNER VOUCHER' }}
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Body Content -->
          <tr>
            <td style="padding: 30px 25px;">
              
              <!-- Greeting -->
              <h2 style="margin: 0 0 10px; font-size: 20px; color: #ffffff; text-align: center;">
                {{ $lead->locale === 'ar' ? 'مبروك، ' . ($lead->name ?: 'عميلنا العزيز') . '!' : 'Congratulations, ' . ($lead->name ?: 'VIP Guest') . '!' }}
              </h2>
              
              <p style="margin: 0 0 25px; font-size: 13px; color: #a1a1aa; text-align: center; line-height: 1.6;">
                {{ $lead->locale === 'ar' 
                  ? 'تم تأكيد وحفظ جائزتك وتفاصيل مشاركتك في جناح مركز فينينو للعناية بالسيارات في معرض أديهيكس 2026 بنجاح.' 
                  : 'Your official prize and reservation details at the Veneno Auto Care Center booth (ADIHEX 2026) have been confirmed and registered.' }}
              </p>

              <!-- Voucher Code Gold Highlight Box -->
              <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 25px; background: #09090b; border: 1.5px dashed #c5a059; border-radius: 14px;">
                <tr>
                  <td align="center" style="padding: 18px 15px;">
                    <div style="font-size: 10px; font-weight: 700; color: #a1a1aa; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">
                      {{ $lead->locale === 'ar' ? 'كود القسيمة الرسمي' : 'OFFICIAL VOUCHER CODE' }}
                    </div>
                    <div style="font-size: 24px; font-weight: 900; font-family: monospace; color: #ef4444; letter-spacing: 2px;">
                      {{ $lead->voucher_code }}
                    </div>
                  </td>
                </tr>
              </table>

              <!-- Details Summary Box -->
              <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #27272a; border-radius: 14px; margin-bottom: 25px;">
                <tr>
                  <td style="padding: 18px;">
                    
                    <!-- Row: Won Prize -->
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 10px;">
                      <tr>
                        <td style="font-size: 12px; color: #a1a1aa;">{{ $lead->locale === 'ar' ? 'الجائزة الرابحة:' : 'Won Prize:' }}</td>
                        <td align="{{ $lead->locale === 'ar' ? 'left' : 'right' }}" style="font-size: 13px; font-weight: bold; color: #f59e0b;">
                          {{ $lead->getPrizeLabel($lead->locale) }}
                        </td>
                      </tr>
                    </table>

                    @if($lead->selected_package_id && $lead->deposit_status === 'paid')
                    <!-- Row: Package -->
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 10px;">
                      <tr>
                        <td style="font-size: 12px; color: #a1a1aa;">{{ $lead->locale === 'ar' ? 'باقة المعرض المحجوزة:' : 'Reserved Show Package:' }}</td>
                        <td align="{{ $lead->locale === 'ar' ? 'left' : 'right' }}" style="font-size: 13px; font-weight: bold; color: #ffffff;">
                          {{ $lead->getPackageName($lead->locale) }}
                        </td>
                      </tr>
                    </table>

                    <!-- Row: Deposit -->
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 10px;">
                      <tr>
                        <td style="font-size: 12px; color: #a1a1aa;">{{ $lead->locale === 'ar' ? 'العربون المدفوع:' : 'Deposit Paid:' }}</td>
                        <td align="{{ $lead->locale === 'ar' ? 'left' : 'right' }}" style="font-size: 13px; font-weight: bold; color: #10b981;">
                          AED 500.00 ({{ $lead->locale === 'ar' ? 'يُخصم 100% من الفاتورة' : '100% Deductible' }})
                        </td>
                      </tr>
                    </table>
                    @endif

                    <!-- Row: Validity -->
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-top: 1px solid #3f3f46; padding-top: 10px;">
                      <tr>
                        <td style="font-size: 11px; color: #a1a1aa;">{{ $lead->locale === 'ar' ? 'صلاحية الاستلام:' : 'Validity Window:' }}</td>
                        <td align="{{ $lead->locale === 'ar' ? 'left' : 'right' }}" style="font-size: 11px; color: #d4d4d8;">
                          {{ $lead->locale === 'ar' ? '60 يوماً من تاريخ الإصدار' : '60 Days from issue date' }}
                        </td>
                      </tr>
                    </table>

                  </td>
                </tr>
              </table>

              <!-- WhatsApp Concierge CTA Button with Official SVG Icon -->
              <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 15px;">
                <tr>
                  <td align="center">
                    <a href="{{ $lead->getWhatsAppUrl() }}" target="_blank" style="display: block; width: 85%; padding: 14px 20px; background-color: #059669; color: #ffffff; text-decoration: none; border-radius: 12px; text-align: center; box-sizing: border-box;">
                      <table border="0" cellspacing="0" cellpadding="0" align="center" style="margin: 0 auto;">
                        <tr>
                          <td style="vertical-align: middle; padding-left: {{ $lead->locale === 'ar' ? '8px' : '0' }}; padding-right: {{ $lead->locale === 'ar' ? '0' : '8px' }};">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="#ffffff" style="display: block;">
                              <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 14.99 3.81 13.47 3.81 11.91C3.81 7.37 7.5 3.67 12.05 3.67M9.53 7.34C9.36 7.34 9.09 7.4 8.87 7.65C8.65 7.89 8.02 8.48 8.02 9.7C8.02 10.92 8.91 12.09 9.03 12.25C9.16 12.42 10.74 14.97 13.25 15.96C15.34 16.79 15.76 16.62 16.22 16.58C16.67 16.54 17.69 15.98 17.9 15.38C18.11 14.78 18.11 14.27 18.05 14.16C17.99 14.05 17.82 13.99 17.57 13.86C17.32 13.74 16.08 13.13 15.85 13.04C15.62 12.96 15.46 12.92 15.29 13.16C15.12 13.41 14.64 13.99 14.5 14.16C14.35 14.32 14.21 14.34 13.96 14.22C13.71 14.09 12.91 13.83 11.96 12.98C11.22 12.32 10.72 11.51 10.58 11.26C10.43 11.01 10.56 10.88 10.69 10.75C10.8 10.64 10.94 10.46 11.06 10.31C11.19 10.17 11.23 10.06 11.31 9.9C11.39 9.73 11.35 9.59 11.29 9.46C11.23 9.34 10.72 8.08 10.51 7.58C10.31 7.09 10.1 7.16 9.94 7.15C9.79 7.14 9.66 7.34 9.53 7.34Z"/>
                            </svg>
                          </td>
                          <td style="vertical-align: middle; font-size: 13px; font-weight: bold; color: #ffffff; text-transform: uppercase; letter-spacing: 0.5px;">
                            {{ $lead->locale === 'ar' ? 'تواصل معنا عبر واتساب' : 'Open WhatsApp Concierge' }}
                          </td>
                        </tr>
                      </table>
                    </a>
                  </td>
                </tr>
              </table>

              <!-- Google Maps Directions Button -->
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center">
                    <a href="https://maps.google.com/?q=Veneno+Auto+Care+Musaffah+M37+Abu+Dhabi" target="_blank" style="display: block; width: 85%; padding: 11px 20px; background-color: #27272a; color: #d4d4d8; font-size: 12px; font-weight: 600; text-decoration: none; border-radius: 12px; text-align: center;">
                      {{ $lead->locale === 'ar' ? 'الموقع الجغرافي للمركز (مصفح M37) 📍' : 'Directions to Workshop (Musaffah M37) 📍' }}
                    </a>
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td align="center" style="padding: 20px; background-color: #09090b; border-top: 1px solid #27272a; font-size: 11px; color: #71717a;">
              Veneno Auto Care Center • Musaffah M37, Abu Dhabi, UAE<br>
              Tel / WhatsApp: +971 2 634 4403 • <a href="https://veneno.ae" style="color: #c5a059; text-decoration: none;">www.veneno.ae</a>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
