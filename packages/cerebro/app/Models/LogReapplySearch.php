<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogReapplySearch extends Model
{
    use HasFactory;

    protected $table = 'logs_reapply_search';

    protected $fillable = [
        'user_id',
        'search_type',
        'found',
        'url',
        ];
}
