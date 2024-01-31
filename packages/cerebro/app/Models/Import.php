<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    protected $table = 'import';

    protected $fillable = [
        'file_name', 
        'status',
        'total_count',
        'applyed_count',
        'errors',
        'warnings'
    ];
    
}
