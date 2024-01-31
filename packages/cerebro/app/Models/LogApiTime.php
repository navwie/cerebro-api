<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogApiTime extends Model
{
    use HasFactory;

    protected $table = 'logs_api_time';

    protected $fillable = [
        'user_agent',
        'request_id',
        'click_id',
        'ip_address',
        'referring_url',
        'action_type',
        'request',
        'response',
        'invalid',
        ];
}
