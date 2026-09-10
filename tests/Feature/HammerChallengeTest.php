<?php

namespace Tests\Feature;

use App\Models\HammerChallengeRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HammerChallengeTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Test Participant',
            'date_of_birth' => '1995-05-20',
            'mobile' => '+971501234567',
            'email' => 'participant@example.test',
            'emergency_contact_name' => 'Emergency Contact',
            'emergency_contact_number' => '+971501234568',
            'age_declaration' => true,
            'health_declaration' => true,
            'challenge_declaration' => true,
            'voluntary_participation' => true,
            'terms_accepted' => true,
            'media_consent' => true,
        ], $overrides);
    }

    public function test_valid_registration_is_saved_and_returns_a_confirmation_token(): void
    {
        $response = $this->postJson('/api/hammer-challenge/register', $this->validPayload());

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('registration.registration_number', 'VHC-0001')
            ->assertJsonStructure(['registration' => ['registration_number', 'confirmation_token', 'full_name', 'status']]);

        $this->assertDatabaseHas('hammer_challenge_registrations', [
            'registration_number' => 'VHC-0001',
            'email' => 'participant@example.test',
            'status' => 'registered',
        ]);
    }

    public function test_under_18_registration_is_rejected_server_side(): void
    {
        $response = $this->postJson('/api/hammer-challenge/register', $this->validPayload([
            'date_of_birth' => now()->subYears(17)->format('Y-m-d'),
        ]));

        $response->assertStatus(422)
            ->assertJsonPath('errors.date_of_birth.0', 'Participants must be at least 18 years old.');
        $this->assertDatabaseCount('hammer_challenge_registrations', 0);
    }

    public function test_duplicate_mobile_or_email_is_rejected(): void
    {
        $this->postJson('/api/hammer-challenge/register', $this->validPayload())->assertCreated();

        $response = $this->postJson('/api/hammer-challenge/register', $this->validPayload([
            'full_name' => 'Second Participant',
        ]));

        $response->assertStatus(409)
            ->assertJsonPath('errors.registration.0', 'This mobile number or email address has already been registered.');
    }

    public function test_confirmation_token_only_returns_safe_confirmation_data(): void
    {
        $this->postJson('/api/hammer-challenge/register', $this->validPayload())->assertCreated();
        $registration = HammerChallengeRegistration::firstOrFail();

        $this->getJson('/api/hammer-challenge/confirmation/' . $registration->confirmation_token)
            ->assertOk()
            ->assertJsonPath('registration.registration_number', 'VHC-0001')
            ->assertJsonMissingPath('registration.email')
            ->assertJsonMissingPath('registration.emergency_contact_number');
    }
}
