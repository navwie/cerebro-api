<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorCard extends Model
{
    use HasFactory;

    protected $table = 'visitors_card';

    protected $casts = [
        'sub_ids' => 'array'
    ];

    protected $fillable = [
        'referral_id',
        'source_url',
        'ip_address',
        'click_id',
        'sub_ids',
        'visits_amount',
        'url',
        'user_agent',
        'date',
    ];
}
