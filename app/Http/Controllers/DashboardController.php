<?php

namespace App\Http\Controllers;

use App\Models\AdihexLead;
use App\Models\HammerChallengeRegistration;
use App\Models\HammerAudienceRegistration;
use App\Models\Inquiry;
use App\Models\User;
use App\Services\SmsGlobalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    /**
     * ADIHEX & Lead Command Center CRM Hub.
     */
    public function index(Request $request): Response
    {
        $adihexLeads = AdihexLead::latest()->get();
        $inquiries = Inquiry::latest()->get();
        $hammerRegistrations = HammerChallengeRegistration::latest()->get();
        $hammerAudiences = HammerAudienceRegistration::latest()->get();
        $users = User::with('roles')->latest()->get()->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'phone' => $u->phone,
                'role' => $u->roles->pluck('name')->first() ?? 'customer',
                'loyalty_tier' => $u->loyalty_tier,
                'created_at' => $u->created_at?->format('Y-m-d H:i'),
            ];
        });

        // ADIHEX 2026 Campaign KPIs
        $adihexTotalSpins = $adihexLeads->count();
        $adihexPaidReservations = $adihexLeads->where('deposit_status', 'paid')->count();
        $adihexDepositCashflow = $adihexLeads->where('deposit_status', 'paid')->sum('deposit_amount');
        $adihexPipelineRevenue = $adihexLeads->where('deposit_status', 'paid')->sum('package_price');
        $adihexRedeemedCount = $adihexLeads->where('is_redeemed', true)->count();
        $adihexActiveCount = $adihexLeads->where('is_redeemed', false)->count();
        $adihexConversionRate = $adihexTotalSpins > 0 ? round(($adihexPaidReservations / $adihexTotalSpins) * 100, 1) : 0;

        $vipReservedCount = $adihexLeads->where('lead_tier', 'VIP_RESERVED')->count();
        $highIntentCount = $adihexLeads->where('lead_tier', 'HIGH_INTENT_PPF')->count();
        $spinPrizeCount = $adihexLeads->where('lead_tier', 'SPIN_PRIZE')->count();

        // Website Quote Leads KPIs
        $inquiriesNew = $inquiries->where('status', 'new')->count();
        $inquiriesContacted = $inquiries->where('status', 'contacted')->count();
        $inquiriesBooked = $inquiries->where('status', 'booked')->count();
        $inquiriesLost = $inquiries->where('status', 'lost')->count();

        return Inertia::render('Dashboard/Index', [
            'adihexLeads' => $adihexLeads,
            'inquiries' => $inquiries,
            'hammerRegistrations' => $hammerRegistrations,
            'hammerAudiences' => $hammerAudiences,
            'users' => $users,
            'adihexStats' => [
                'totalSpins' => $adihexTotalSpins,
                'paidReservations' => $adihexPaidReservations,
                'depositCashflow' => $adihexDepositCashflow,
                'pipelineRevenue' => $adihexPipelineRevenue,
                'redeemedCount' => $adihexRedeemedCount,
                'activeCount' => $adihexActiveCount,
                'conversionRate' => $adihexConversionRate,
                'vipReservedCount' => $vipReservedCount,
                'highIntentCount' => $highIntentCount,
                'spinPrizeCount' => $spinPrizeCount,
            ],
            'inquiryStats' => [
                'total' => $inquiries->count(),
                'new' => $inquiriesNew,
                'contacted' => $inquiriesContacted,
                'booked' => $inquiriesBooked,
                'lost' => $inquiriesLost,
            ],
        ]);
    }

    /**
     * Update Inquiry Status
     */
    public function updateInquiryStatus(Request $request, Inquiry $inquiry)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,contacted,booked,lost',
        ]);

        $inquiry->update($validated);

        return back()->with('success', 'Inquiry status updated.');
    }

    /**
     * Delete Inquiry
     */
    public function destroyInquiry(Inquiry $inquiry)
    {
        $inquiry->delete();

        return back()->with('success', 'Inquiry deleted successfully.');
    }

    /**
     * Change logged-in user password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Your password was updated successfully.');
    }

    /**
     * Reset specific staff member's password.
     */
    public function resetUserPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', "Password for {$user->name} ({$user->email}) was successfully reset.");
    }

    public function updateHammerRegistrationStatus(Request $request, HammerChallengeRegistration $registration)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:registered,checked_in,participated,disqualified,finished'],
        ]);

        $registration->update(['status' => $validated['status']]);

        return back()->with('success', 'Participant status updated successfully.');
    }

    public function updateHammerAudienceStatus(Request $request, HammerAudienceRegistration $audience)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:registered,checked_in,attended,cancelled'],
        ]);

        $audience->update(['status' => $validated['status']]);

        return back()->with('success', 'Audience visitor status updated successfully.');
    }

    public function sendHammerWinnerSms(Request $request, HammerAudienceRegistration $audience, SmsGlobalService $smsService): JsonResponse
    {
        $customMessage = $request->input('message');
        $result = $smsService->sendHammerWinnerSms($audience, $customMessage);

        if ($result['success'] ?? false) {
            $audience->update([
                'sms_sent_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Winner SMS successfully sent to {$audience->full_name} ({$audience->mobile}).",
                'sms_sent_at' => $audience->sms_sent_at?->format('H:i • d M'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['error'] ?? $result['message'] ?? 'Failed to send SMS via SMSGlobal gateway.',
        ], 422);
    }

    public function exportHammerAudience(): StreamedResponse
    {
        $fileName = 'veneno_hammer_audience_' . now()->format('Y_m_d_His') . '.csv';
        $audiences = HammerAudienceRegistration::latest()->get();
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ];

        return response()->stream(function () use ($audiences) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Ticket Number', 'Full Name', 'Mobile', 'Email', 'Google Review Account', 'Registration Date & Time', 'Status',
            ]);

            foreach ($audiences as $audience) {
                fputcsv($file, [
                    $audience->ticket_number,
                    $audience->full_name,
                    $audience->mobile,
                    $audience->email ?? 'N/A',
                    $audience->google_review_name ?? 'N/A',
                    $audience->created_at?->format('Y-m-d H:i:s'),
                    $audience->status,
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }

    public function exportHammerRegistrations(): StreamedResponse
    {
        $fileName = 'veneno_hammer_challenge_' . now()->format('Y_m_d_His') . '.csv';
        $registrations = HammerChallengeRegistration::latest()->get();
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ];

        return response()->stream(function () use ($registrations) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Registration Number', 'Full Name', 'Date of Birth', 'Age', 'Mobile', 'Email',
                'Emergency Contact Name', 'Emergency Contact Number', 'Registration Date & Time',
                'Health Declaration', 'Terms Accepted', 'Media Consent', 'Status',
            ]);

            foreach ($registrations as $registration) {
                fputcsv($file, [
                    $registration->registration_number,
                    $registration->full_name,
                    $registration->getRawOriginal('date_of_birth'),
                    $registration->age,
                    $registration->mobile,
                    $registration->email,
                    $registration->emergency_contact_name,
                    $registration->emergency_contact_number,
                    $registration->created_at?->format('Y-m-d H:i:s'),
                    $registration->health_declaration ? 'Yes' : 'No',
                    $registration->terms_accepted ? 'Yes' : 'No',
                    $registration->media_consent ? 'Yes' : 'No',
                    $registration->status,
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }
}
