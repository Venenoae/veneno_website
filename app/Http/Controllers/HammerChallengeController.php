<?php

namespace App\Http\Controllers;

use App\Models\HammerChallengeRegistration;
use App\Models\HammerAudienceRegistration;
use App\Services\SmsGlobalService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class HammerChallengeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('HammerChallenge/Index');
    }

    public function display(): Response
    {
        return Inertia::render('HammerChallenge/Display', [
            'targetUrl' => url('/hammer-challenge/audience'),
        ]);
    }

    public function audience(Request $request, ?string $locale = null): Response
    {
        if ($locale && in_array($locale, ['en', 'ar'])) {
            app()->setLocale($locale);
        }

        return Inertia::render('HammerChallenge/Audience', [
            'initialLocale' => $locale ?: app()->getLocale(),
        ]);
    }

    public function audienceConfirmation(): Response
    {
        return Inertia::render('HammerChallenge/AudienceConfirmation');
    }

    public function terms(): Response
    {
        return Inertia::render('HammerChallenge/Terms');
    }

    public function confirmation(): Response
    {
        return Inertia::render('HammerChallenge/Confirmation');
    }

    public function register(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Contestant registration for the Veneno Hammer Challenge is now closed.',
            'errors' => ['registration' => ['Contestant registration is now closed.']],
        ], 422);
    }

    public function confirmationData(string $token): JsonResponse
    {
        $registration = HammerChallengeRegistration::query()
            ->where('confirmation_token', $token)
            ->first();

        if (!$registration) {
            return response()->json(['message' => 'Confirmation not found.'], 404);
        }

        return response()->json([
            'registration' => [
                'registration_number' => $registration->registration_number,
                'full_name' => $registration->full_name,
                'status' => $registration->status,
            ],
        ]);
    }

    public function registerAudience(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'min:2', 'max:120'],
            'mobile' => ['required', 'string', 'regex:/^\+?[0-9\s().-]{7,20}$/', 'max:30'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'google_review_name' => ['nullable', 'string', 'max:150'],
        ]);

        $validated['full_name'] = trim($validated['full_name']);
        $validated['mobile'] = $this->normalizePhone($validated['mobile']);
        $validated['email'] = !empty($validated['email']) ? strtolower(trim($validated['email'])) : null;
        $validated['google_review_name'] = !empty($validated['google_review_name']) ? trim($validated['google_review_name']) : null;

        // Check if already registered
        $existing = HammerAudienceRegistration::query()
            ->where('mobile', $validated['mobile'])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'already_registered' => true,
                'message' => 'You are already registered for an Audience Pass.',
                'registration' => [
                    'ticket_number' => $existing->ticket_number,
                    'confirmation_token' => $existing->confirmation_token,
                    'full_name' => $existing->full_name,
                    'google_review_name' => $existing->google_review_name,
                    'status' => $existing->status,
                ],
            ], 200);
        }

        $registration = DB::transaction(function () use ($validated, $request) {
            $registration = HammerAudienceRegistration::create([
                'ticket_number' => 'VHA-' . strtoupper(Str::random(12)),
                'confirmation_token' => Str::random(64),
                'full_name' => $validated['full_name'],
                'mobile' => $validated['mobile'],
                'email' => $validated['email'],
                'google_review_name' => $validated['google_review_name'],
                'status' => 'registered',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $registration->update([
                'ticket_number' => 'VHA-' . str_pad((string) $registration->id, 4, '0', STR_PAD_LEFT),
            ]);

            return $registration->fresh();
        });

        return response()->json([
            'success' => true,
            'registration' => [
                'ticket_number' => $registration->ticket_number,
                'confirmation_token' => $registration->confirmation_token,
                'full_name' => $registration->full_name,
                'google_review_name' => $registration->google_review_name,
                'status' => $registration->status,
            ],
        ], 201);
    }

    public function audienceConfirmationData(string $token): JsonResponse
    {
        $registration = HammerAudienceRegistration::query()
            ->where('confirmation_token', $token)
            ->first();

        if (!$registration) {
            return response()->json(['message' => 'Audience confirmation pass not found.'], 404);
        }

        return response()->json([
            'registration' => [
                'ticket_number' => $registration->ticket_number,
                'full_name' => $registration->full_name,
                'mobile' => $registration->mobile,
                'email' => $registration->email,
                'google_review_name' => $registration->google_review_name,
                'status' => $registration->status,
                'created_at' => $registration->created_at?->format('Y-m-d H:i'),
            ],
        ]);
    }

    public function raffle(Request $request): Response
    {
        $eligibleCount = HammerAudienceRegistration::eligibleForRaffle()->count();
        $recentWinners = HammerAudienceRegistration::winners()
            ->take(10)
            ->get(['id', 'full_name', 'ticket_number', 'won_at']);

        return Inertia::render('HammerChallenge/Raffle', [
            'initialEligibleCount' => $eligibleCount,
            'recentWinners' => $recentWinners,
        ]);
    }

    public function getRaffleParticipants(): JsonResponse
    {
        $participants = HammerAudienceRegistration::eligibleForRaffle()
            ->get(['id', 'full_name', 'ticket_number']);

        $winners = HammerAudienceRegistration::winners()
            ->get(['id', 'full_name', 'ticket_number', 'won_at']);

        return response()->json([
            'success' => true,
            'count' => $participants->count(),
            'participants' => $participants,
            'winners' => $winners,
        ]);
    }

    public function drawRaffleWinner(Request $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $participantId = $request->input('participant_id');

            $query = HammerAudienceRegistration::query()
                ->where('is_winner', false)
                ->lockForUpdate();

            if ($participantId) {
                $winner = $query->find($participantId);
            } else {
                $winner = $query->inRandomOrder()->first();
            }

            if (!$winner) {
                return response()->json([
                    'success' => false,
                    'message' => 'No eligible participants found for the raffle draw.',
                ], 404);
            }

            $winner->update([
                'is_winner' => true,
                'won_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'winner' => [
                    'id' => $winner->id,
                    'full_name' => $winner->full_name,
                    'ticket_number' => $winner->ticket_number,
                    'won_at' => $winner->won_at?->format('H:i:s • d M Y'),
                ],
                'remaining_count' => HammerAudienceRegistration::eligibleForRaffle()->count(),
            ]);
        });
    }

    public function resetRaffleWinners(): JsonResponse
    {
        HammerAudienceRegistration::query()->update([
            'is_winner' => false,
            'won_at' => null,
            'prize_claimed' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'All raffle winners have been reset.',
            'count' => HammerAudienceRegistration::count(),
        ]);
    }

    public function sendRaffleWinnerSms(Request $request, HammerAudienceRegistration $audience, SmsGlobalService $smsService): JsonResponse
    {
        $customMessage = $request->input('message');
        $result = $smsService->sendHammerWinnerSms($audience, $customMessage);

        if ($result['success'] ?? false) {
            $audience->update([
                'sms_sent_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Winner notification SMS sent to {$audience->mobile}!",
                'sms_sent_at' => $audience->sms_sent_at?->format('H:i • d M'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['error'] ?? $result['message'] ?? 'SMS dispatch failed.',
        ], 422);
    }

    private function normalizePhone(string $phone): string
    {
        $normalized = preg_replace('/[^+0-9]/', '', trim($phone));

        if (str_starts_with($normalized, '05')) {
            return '+971' . substr($normalized, 1);
        }

        if (str_starts_with($normalized, '971')) {
            return '+' . $normalized;
        }

        return $normalized;
    }
}
