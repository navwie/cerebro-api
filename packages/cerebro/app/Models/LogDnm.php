<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogDnm extends Model
{
    use HasFactory;

    protected $table = 'logs_dnm';

    protected $fillable = [
        'log_id',
        'sent_dnm',
        'response_dnm',
        ];
}
