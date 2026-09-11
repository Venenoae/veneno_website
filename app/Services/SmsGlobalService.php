<?php

namespace App\Services;

use App\Models\AdihexLead;
use App\Models\HammerAudienceRegistration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SmsGlobalService
{
    protected string $apiKey;
    protected string $apiSecret;
    protected ?string $senderId;

    public function __construct()
    {
        $this->apiKey = (string) (config('services.smsglobal.key') ?: env('SMSGLOBAL_KEY', ''));
        $this->apiSecret = (string) (config('services.smsglobal.secret') ?: env('SMSGLOBAL_SECRET', ''));
        $this->senderId = (string) (config('services.smsglobal.sender_id') ?: env('SMSGLOBAL_SENDER_ID', 'VENENO AE'));
    }

    /**
     * Normalize UAE/International Phone Number for SMSGlobal
     */
    public function normalizePhone(string $phone): string
    {
        // Strip non-digits
        $cleaned = preg_replace('/[^\d]/', '', $phone);

        // Strip leading 00 (e.g. 00971 -> 971)
        if (str_starts_with($cleaned, '00')) {
            $cleaned = substr($cleaned, 2);
        }

        // If local UAE number like 0501234567 -> 971501234567
        if (str_starts_with($cleaned, '05')) {
            $cleaned = '971' . substr($cleaned, 1);
        } elseif (str_starts_with($cleaned, '5') && strlen($cleaned) === 9) {
            $cleaned = '971' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Send SMS via SMSGlobal REST API v2 (HMAC Authentication)
     */
    public function sendSms(string $toPhone, string $message): array
    {
        $normalizedRecipient = $this->normalizePhone($toPhone);

        if (empty($this->apiKey) || empty($this->apiSecret)) {
            Log::warning('[SmsGlobal] API Key or Secret not configured. SMS logged only.', [
                'to' => $normalizedRecipient,
                'message' => $message,
            ]);

            return [
                'success' => false,
                'status' => 'not_configured',
                'message' => 'SMSGlobal credentials not configured in .env',
            ];
        }

        try {
            $timestamp = time();
            $nonce = Str::random(16);
            $method = 'POST';
            $uri = '/v2/sms';
            $host = 'api.smsglobal.com';
            $port = '443';

            $rawString = "{$timestamp}\n{$nonce}\n{$method}\n{$uri}\n{$host}\n{$port}\n\n";
            $hash = base64_encode(hash_hmac('sha256', $rawString, $this->apiSecret, true));
            $authHeader = sprintf('MAC id="%s", ts="%s", nonce="%s", mac="%s"', $this->apiKey, $timestamp, $nonce, $hash);

            $payload = [
                'destination' => $normalizedRecipient,
                'message' => $message,
                'origin' => $this->senderId ?: 'VENENO AE',
            ];

            $response = Http::timeout(12)->withHeaders([
                'Authorization' => $authHeader,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://api.smsglobal.com/v2/sms', $payload);

            $body = $response->json();
            $isSuccess = $response->successful();

            Log::info('[SmsGlobal] SMS Dispatch Result', [
                'to' => $normalizedRecipient,
                'status' => $response->status(),
                'response' => $body,
            ]);

            return [
                'success' => $isSuccess,
                'response' => $body,
            ];
        } catch (\Exception $e) {
            Log::error('[SmsGlobal] Error sending SMS: ' . $e->getMessage(), [
                'to' => $normalizedRecipient,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send ADIHEX Automated Step 7 Pass & Voucher SMS
     */
    public function sendAdihexVoucherSms(AdihexLead $lead): array
    {
        $isAr = ($lead->locale === 'ar');
        $code = $lead->voucher_code;
        $hasPackage = ($lead->deposit_status === 'paid' && $lead->selected_package_name);

        if ($isAr) {
            $prize = $lead->getPrizeLabel('ar');
            $pkgName = $lead->getPackageName('ar');

            if ($hasPackage) {
                $message = "مبروك! تم تأكيد حجزك في أديهيكس 2026 لدى فينينو للعناية بالسيارات.\n"
                    . "الباقة: {$pkgName}\n"
                    . "العربون: 500 درهم مدفوع (يُخصم بالكامل)\n"
                    . "كود القسيمة: {$code}\n"
                    . "جائزة السحب: {$prize}\n"
                    . "صلاحية العرض: 60 يوماً.\n"
                    . "واتساب: +97126344403";
            } else {
                $message = "مبروك! تم تسجيل جائزتك من فينينو للعناية بالسيارات في أديهيكس 2026.\n"
                    . "الجائزة: {$prize}\n"
                    . "كود القسيمة: {$code}\n"
                    . "صلاحية الاستلام: 60 يوماً.\n"
                    . "واتساب: +97126344403";
            }
        } else {
            $prize = $lead->getPrizeLabel('en');
            $pkgName = $lead->getPackageName('en');

            if ($hasPackage) {
                $message = "Mabrook! Your Veneno Auto Care ADIHEX 2026 reservation is confirmed.\n"
                    . "Package: {$pkgName}\n"
                    . "Deposit: AED 500 Paid (100% Deductible)\n"
                    . "Voucher Code: {$code}\n"
                    . "Won Prize: {$prize}\n"
                    . "Validity: 60 Days.\n"
                    . "WhatsApp: +97126344403";
            } else {
                $message = "Mabrook! Your Veneno Auto Care ADIHEX 2026 prize is confirmed.\n"
                    . "Won Prize: {$prize}\n"
                    . "Voucher Code: {$code}\n"
                    . "Validity: 60 Days.\n"
                    . "WhatsApp: +97126344403";
            }
        }

        return $this->sendSms($lead->phone, $message);
    }

    /**
     * Send Hammer Challenge Audience Raffle Winner Notification SMS
     */
    public function sendHammerWinnerSms(HammerAudienceRegistration $audience, ?string $customMessage = null): array
    {
        if ($customMessage && trim($customMessage)) {
            return $this->sendSms($audience->mobile, trim($customMessage));
        }

        $name = $audience->full_name;
        $ticket = $audience->ticket_number;

        $message = "مبروك للفائز {$name}! 🎉\n"
            . "تهانينا! لقد فازت تذكرتك ({$ticket}) رسمياً بسحب جوائز الجمهور في تحدي مطرقة فينينو 2026.\n"
            . "يرجى التوجه للمنصة أو مراجعة إدارة المركز لاستلام جائزتك.\n"
            . "مركز فينينو للعناية بالسيارات - مصفح M37، أبوظبي\n"
            . "هاتف: +97126344403";

        return $this->sendSms($audience->mobile, $message);
    }
}
