<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
class CreditCard extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $table = 'customer_credit_cards';

    protected $casts = [
        'sub_ids' => 'array'
    ];

    protected $fillable = [
        'first_name',
        'referral_id',
        'last_name',
        'email',
        'state',
        'click_id',
        'sub_ids',
        'user_agent',
        'ip_address',
        'referring_url',
    ];
}
