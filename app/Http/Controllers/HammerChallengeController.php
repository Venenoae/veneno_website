<?php

namespace App\Http\Controllers;

use App\Models\HammerChallengeRegistration;
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

    public function terms(): Response
    {
        return Inertia::render('HammerChallenge/Terms');
    }

    public function display(Request $request): Response
    {
        return Inertia::render('HammerChallenge/Display', [
            'targetUrl' => url('/hammer-challenge/register'),
        ]);
    }

    public function confirmation(): Response
    {
        return Inertia::render('HammerChallenge/Confirmation');
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'min:2', 'max:120'],
            'mobile' => ['required', 'string', 'regex:/^\+?[0-9\s().-]{7,20}$/', 'max:30'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'terms_accepted' => ['accepted'],
        ]);

        $fullName = trim($validated['full_name']);
        $mobile = $this->normalizePhone($validated['mobile']);
        $email = !empty($validated['email']) ? strtolower(trim($validated['email'])) : null;

        // Check if mobile or non-null email already registered
        $query = HammerChallengeRegistration::query()->where('mobile', $mobile);
        if ($email) {
            $query->orWhere('email', $email);
        }

        if ($query->exists()) {
            return response()->json([
                'message' => 'This mobile number or email address has already been registered.',
                'errors' => ['registration' => ['This mobile number or email address has already been registered.']],
            ], 409);
        }

        $registration = DB::transaction(function () use ($fullName, $mobile, $email, $request) {
            $registration = HammerChallengeRegistration::create([
                'full_name' => $fullName,
                'mobile' => $mobile,
                'email' => $email,
                'age_declaration' => true,
                'health_declaration' => true,
                'challenge_declaration' => true,
                'voluntary_participation' => true,
                'terms_accepted' => true,
                'media_consent' => true,
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
