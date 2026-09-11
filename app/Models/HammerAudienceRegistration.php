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
        'status',
        'ip_address',
        'user_agent',
    ];
}
