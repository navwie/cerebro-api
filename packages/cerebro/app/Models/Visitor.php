<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $casts = [
        'sub_ids' => 'array'
    ];

    protected $fillable = [
        'ip_address',
        'referral_id',
        'ref_url',
        'visits_amount',
        'clicks_amount',
        'url',
        'user_agent',
        'step',
        'date',
        'click_id',
        'sub_ids',
        'flow_id',
        'action_type',
        'cookie_mark',
    ];
}
