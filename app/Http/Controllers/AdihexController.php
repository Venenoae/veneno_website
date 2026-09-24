<?php

namespace App\Http\Controllers;

use App\Models\AdihexLead;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdihexController extends Controller
{
    /**
     * Prize Table Configuration & Probabilities (ADIHEX 2026 Official Daily Allocation)
     * Total Daily Allocation: 92 Vouchers | 100%
     */
    private array $prizes = [
        [
            'id' => 'polish_detailing',
            'label_en' => 'Free Polish & Detailing',
            'label_ar' => 'بوليش وتلميع مجاني',
            'value_en' => 'Worth AED 650 • High Excitement',
            'value_ar' => 'بقيمة 650 درهم • لمعان استثنائي',
            'weight' => 1,
            'daily_limit' => 1, // 1.09% (1/92)
            'color' => '#C5A059', // Champagne Gold
            'textColor' => '#0A0A0A',
        ],
        [
            'id' => 'tint_20',
            'label_en' => '20% Off Window Tinting',
            'label_ar' => 'خصم 20% على التظليل',
            'value_en' => 'Premium Nano-Ceramic Heat Rejection',
            'value_ar' => 'عزل حراري نانو سيراميك فاخر',
            'weight' => 20,
            'daily_limit' => 20, // 21.74% (20/92)
            'color' => '#1A1A1A', // Deep Carbon
            'textColor' => '#E5C07B',
        ],
        [
            'id' => 'voucher_100',
            'label_en' => 'AED 100 Gift Voucher',
            'label_ar' => 'قسيمة 100 درهم',
            'value_en' => 'Direct credit towards bookings',
            'value_ar' => 'رصيد مباشر للحجوزات',
            'weight' => 10,
            'daily_limit' => 10, // 10.87% (10/92)
            'color' => '#8B0000', // Deep Crimson
            'textColor' => '#FFFFFF',
        ],
        [
            'id' => 'wash_diamond',
            'label_en' => 'Free Diamond Car Wash',
            'label_ar' => 'غسيل دايموند مجاني',
            'value_en' => 'Worth AED 250 • Complimentary',
            'value_ar' => 'بقيمة 250 درهم • مجاناً',
            'weight' => 5,
            'daily_limit' => 5, // 5.43% (5/92)
            'color' => '#C5A059', // Champagne Gold
            'textColor' => '#0A0A0A',
        ],
        [
            'id' => 'wash_slime',
            'label_en' => 'Free Slime Wash',
            'label_ar' => 'غسيل سلايم مجاني',
            'value_en' => 'Worth AED 180 • Complimentary',
            'value_ar' => 'بقيمة 180 درهم • مجاناً',
            'weight' => 1,
            'daily_limit' => 1, // 1.09% (1/92)
            'color' => '#1A1A1A', // Deep Carbon
            'textColor' => '#E5C07B',
        ],
        [
            'id' => 'discount_20',
            'label_en' => '20% Off All Services',
            'label_ar' => 'خصم 20% على جميع الخدمات',
            'value_en' => 'Valid on any service at Veneno',
            'value_ar' => 'صالحة على جميع خدمات فينينو',
            'weight' => 50,
            'daily_limit' => 50, // 54.35% (50/92)
            'color' => '#8B0000', // Deep Crimson
            'textColor' => '#FFFFFF',
        ],
        [
            'id' => 'platinum_20',
            'label_en' => '20% Off Platinum Package',
            'label_ar' => 'خصم 20% على باقة Platinum',
            'value_en' => 'Full Body G100 Self-Healing PPF',
            'value_ar' => 'حماية كاملة بجلاد PPF الذاتي',
            'weight' => 5,
            'daily_limit' => 5, // 5.43% (5/92)
            'color' => '#E50914', // Glowing Crimson Red
            'textColor' => '#FFFFFF',
        ],
    ];

    /**
     * ADIHEX Exclusive Show Packages
     */
    private array $packages = [
        [
            'id' => 'show_special',
            'tier' => 1,
            'badge' => '🔥 SHOW SPECIAL',
            'badge_ar' => '🔥 عرض المعرض الخاص',
            'name_en' => 'Detailing + Tinting Package',
            'name_ar' => 'باقة التلميع الشامل + التظليل الحراري',
            'desc_en' => 'Full Deep Clean Polish & Premium Ceramic Heat Rejection Window Film',
            'desc_ar' => 'تلميع ساطع مع فيلم عازل للحرارة نانو سيراميك فائق الجودة',
            'image' => '/images/adihex/packages/pkg_show_special.jpg',
            'original_price' => 2500,
            'promo_price' => 1699,
            'deposit' => 50,
        ],
        [
            'id' => 'silver',
            'tier' => 2,
            'badge' => 'SILVER TIER',
            'badge_ar' => 'الفئة الفضية',
            'name_en' => 'Silver Package',
            'name_ar' => 'الباقة الفضية',
            'desc_en' => '9H Nano Ceramic Coating (3-Year Warranty) + Multi-Stage Paint Correction',
            'desc_ar' => 'طلاء نانو سيراميك 9H (ضمان 3 سنوات) مع معالجة وتصحيح الطلاء',
            'image' => '/images/adihex/packages/pkg_silver.jpg',
            'original_price' => 2730,
            'promo_price' => 1899,
            'deposit' => 50,
        ],
        [
            'id' => 'golden',
            'tier' => 3,
            'badge' => '⭐ BEST VALUE',
            'badge_ar' => '⭐ القيمة الأفضل',
            'name_en' => 'Golden Package',
            'name_ar' => 'الباقة الذهبية',
            'desc_en' => 'Front-End PPF Armor + 5-Year Dual-Layer Ceramic Body & Interior Protection',
            'desc_ar' => 'حماية PPF للمقدمة + سيراميك مزدوج 5 سنوات للهيكل والمقصورة',
            'image' => '/images/adihex/packages/pkg_golden.jpg',
            'original_price' => 5775,
            'promo_price' => 3999,
            'deposit' => 50,
        ],
        [
            'id' => 'platinum',
            'tier' => 4,
            'badge' => '👑 ULTRA PRESTIGE',
            'badge_ar' => '👑 الحماية الملكية الفائقة',
            'name_en' => 'Platinum Package',
            'name_ar' => 'باقة البلاتينيوم الملكية',
            'desc_en' => 'Full Body G100 Self-Healing PPF (10-Yr) + Rock 5-Yr Tint + Full Interior & Rim Ceramic',
            'desc_ar' => 'حماية كاملة بالجلاد الذاتي G100 (ضمان 10 سنوات) + تظليل 5 سنوات + سيراميك شامل للجنوط والمقصورة',
            'image' => '/images/adihex/packages/pkg_platinum.jpg',
            'original_price' => 15000,
            'promo_price' => 9999,
            'deposit' => 50,
        ],
    ];

    /**
     * ADIHEX Campaign is concluded - Redirect to Home.
     */
    public function index(Request $request, ?string $locale = null)
    {
        return redirect()->route('home');
    }

    /**
     * ADIHEX Display screen is concluded - Redirect to Home.
     */
    public function display(Request $request, ?string $locale = null)
    {
        return redirect()->route('home');
    }

    /**
     * ADIHEX Terms is concluded - Redirect to Home.
     */
    public function terms(Request $request, ?string $locale = null)
    {
        return redirect()->route('home');
    }

    /**
     * ADIHEX Campaign is concluded. Public spin endpoint is closed.
     */
    public function spin(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'The ADIHEX 2026 campaign has concluded. Prize spins and submissions are now closed.',
        ], 410);
    }

    /**
     * ADIHEX Campaign is concluded. Public reservation endpoint is closed.
     */
    public function reserve(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'The ADIHEX 2026 campaign has concluded. Show package reservations are now closed.',
        ], 410);
    }

    /**
     * ADIHEX Campaign is concluded. Payment intents are disabled.
     */
    public function createPaymentIntent(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'The ADIHEX 2026 campaign has concluded. Online deposits are closed.',
        ], 410);
    }

    /**
     * Redeem Voucher Code (CRM or Reception Action)
     */
    public function redeemVoucher(Request $request)
    {
        $validated = $request->validate([
            'voucher_code' => 'required|string',
        ]);

        $code = strtoupper(trim($validated['voucher_code']));
        $lead = AdihexLead::where('voucher_code', $code)->first();

        if (!$lead) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher code not found.',
            ], 404);
        }

        if ($lead->is_redeemed) {
            return response()->json([
                'success' => false,
                'message' => 'This voucher was already redeemed on ' . $lead->redeemed_at->format('M d, Y H:i'),
                'lead' => $lead,
            ], 422);
        }

        $lead->update([
            'is_redeemed' => true,
            'redeemed_at' => Carbon::now(),
            'status' => 'redeemed',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Voucher redeemed successfully for ' . $lead->name,
            'lead' => $lead,
        ]);
    }

    /**
     * Export ADIHEX Leads to CSV
     */
    public function exportLeads(): StreamedResponse
    {
        $fileName = 'veneno_adihex_leads_' . Carbon::now()->format('Y_m_d_His') . '.csv';
        $leads = AdihexLead::latest()->get();

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'ID',
            'Full Name',
            'Phone',
            'Email',
            'Lead Tier',
            'Won Prize',
            'Voucher Code',
            'Expires At',
            'Is Redeemed',
            'Redeemed At',
            'Selected Package',
            'Package Price (AED)',
            'Deposit Amount (AED)',
            'Deposit Status',
            'Service Intent',
            'Locale',
            'Created At'
        ];

        return response()->stream(function () use ($leads, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($leads as $lead) {
                fputcsv($file, [
                    $lead->id,
                    $lead->name,
                    $lead->phone,
                    $lead->email ?? 'N/A',
                    $lead->lead_tier,
                    $lead->won_prize_label,
                    $lead->voucher_code,
                    $lead->voucher_expires_at ? $lead->voucher_expires_at->format('Y-m-d') : '',
                    $lead->is_redeemed ? 'Yes' : 'No',
                    $lead->redeemed_at ? $lead->redeemed_at->format('Y-m-d H:i:s') : '',
                    $lead->selected_package_name ?? 'None',
                    $lead->package_price ?? 0,
                    $lead->deposit_amount,
                    $lead->deposit_status,
                    is_array($lead->service_intent) ? implode(', ', $lead->service_intent) : '',
                    $lead->locale,
                    $lead->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }

    /**
     * Helper to pick weighted random index
     */
    private function calculateWeightedRandom(array $weights): int
    {
        $totalWeight = array_sum($weights);
        if ($totalWeight <= 0) {
            return 0;
        }

        $rand = rand(1, $totalWeight);
        $cumulative = 0;

        foreach ($weights as $index => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return (int)$index;
            }
        }

        return 0;
    }
}
