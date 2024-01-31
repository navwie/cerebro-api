<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogRedirected extends Model
{
    use HasFactory;

    protected $table = 'logs_redirected';

    protected $fillable = [
        'decision_id',
        'decision_audit_id',
        ];
}
