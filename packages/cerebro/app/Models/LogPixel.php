<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogPixel extends Model
{
    use HasFactory;

    protected $table = 'logs_pixel';

    protected $fillable = [
        'email',
        'reapply_id',
        'customer_id',
        'decision_id',
        'action_type',
        'request_id',
        'click_id',
        'transaction_id',
        'referring_url',
        'user_agent',
        'ip_address',
        'sent_pixel',
        'response_pixel',
    ];
}
