<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HammerAudienceRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'confirmation_token',
        'full_name',
        'mobile',
        'email',
        'google_review_name',
        'is_winner',
        'won_at',
        'prize_claimed',
        'status',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'is_winner' => 'boolean',
        'prize_claimed' => 'boolean',
        'won_at' => 'datetime',
    ];

    public function scopeEligibleForRaffle($query)
    {
        return $query->where('is_winner', false);
    }

    public function scopeWinners($query)
    {
        return $query->where('is_winner', true)->orderBy('won_at', 'desc');
    }
}
