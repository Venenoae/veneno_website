<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HammerChallengeRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_number',
        'confirmation_token',
        'full_name',
        'date_of_birth',
        'age',
        'mobile',
        'email',
        'emergency_contact_name',
        'emergency_contact_number',
        'age_declaration',
        'health_declaration',
        'challenge_declaration',
        'voluntary_participation',
        'terms_accepted',
        'media_consent',
        'status',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'date_of_birth' => 'date:Y-m-d',
        'age' => 'integer',
        'age_declaration' => 'boolean',
        'health_declaration' => 'boolean',
        'challenge_declaration' => 'boolean',
        'voluntary_participation' => 'boolean',
        'terms_accepted' => 'boolean',
        'media_consent' => 'boolean',
    ];
}
