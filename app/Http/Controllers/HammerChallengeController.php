<?php

namespace App\Http\Controllers;

use App\Models\HammerChallengeRegistration;
use App\Models\HammerAudienceRegistration;
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

    public function audience(): Response
    {
        return Inertia::render('HammerChallenge/Audience');
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
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'min:2', 'max:120'],
            'date_of_birth' => ['required', 'date_format:Y-m-d', 'before:today'],
            'mobile' => ['required', 'string', 'regex:/^\\+?[0-9\\s().-]{7,20}$/', 'max:30'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'emergency_contact_name' => ['required', 'string', 'min:2', 'max:120'],
            'emergency_contact_number' => ['required', 'string', 'regex:/^\\+?[0-9\\s().-]{7,20}$/', 'max:30'],
            'terms_accepted' => ['accepted'],
        ]);

        $validated['email'] = strtolower(trim($validated['email']));
        $validated['mobile'] = $this->normalizePhone($validated['mobile']);
        $validated['emergency_contact_number'] = trim($validated['emergency_contact_number']);
        $validated['age_declaration'] = true;
        $validated['health_declaration'] = true;
        $validated['challenge_declaration'] = true;
        $validated['voluntary_participation'] = true;
        $validated['media_consent'] = true;

        $dateOfBirth = Carbon::createFromFormat('Y-m-d', $validated['date_of_birth'])->startOfDay();
        $age = $dateOfBirth->age;

        if ($age < 18) {
            return response()->json([
                'message' => 'Participants must be at least 18 years old.',
                'errors' => ['date_of_birth' => ['Participants must be at least 18 years old.']],
            ], 422);
        }

        if (HammerChallengeRegistration::query()
            ->where('mobile', $validated['mobile'])
            ->orWhere('email', $validated['email'])
            ->exists()) {
            return response()->json([
                'message' => 'This mobile number or email address has already been registered.',
                'errors' => ['registration' => ['This mobile number or email address has already been registered.']],
            ], 409);
        }

        $registration = DB::transaction(function () use ($validated, $dateOfBirth, $age, $request) {
            $registration = HammerChallengeRegistration::create([
                ...$validated,
                'date_of_birth' => $dateOfBirth->toDateString(),
                'age' => $age,
                'registration_number' => 'VHC-' . strtoupper(Str::random(16)),
                'confirmation_token' => Str::random(64),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $registration->update([
                'registration_number' => 'VHC-' . str_pad((string) $registration->id, 4, '0', STR_PAD_LEFT),
            ]);

            return $registration->fresh();
        });

        return response()->json([
            'success' => true,
            'registration' => [
                'registration_number' => $registration->registration_number,
                'confirmation_token' => $registration->confirmation_token,
                'full_name' => $registration->full_name,
                'status' => $registration->status,
            ],
        ], 201);
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
        ]);

        $validated['full_name'] = trim($validated['full_name']);
        $validated['mobile'] = $this->normalizePhone($validated['mobile']);
        $validated['email'] = !empty($validated['email']) ? strtolower(trim($validated['email'])) : null;

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
                'status' => $registration->status,
                'created_at' => $registration->created_at?->format('Y-m-d H:i'),
            ],
        ]);
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
