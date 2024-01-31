<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomersToVisitors extends Model
{
    use HasFactory;
    protected $table = 'customers_to_visitors';

    protected $fillable = [
        'visitor_id',
        'customer_id',
        'ip',
        'user_agent',
        'click_id',
        'email',
        'type',
    ];
}
