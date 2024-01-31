<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditCardAudit extends Model
{
    use HasFactory;

    protected $connection = 'mysql_audit';
    protected $table = 'customer_credit_cards';

    protected $casts = [
        'sub_ids' => 'array'
    ];

    protected $fillable = [
        'log_id',
        'referral_id',
        'first_name',
        'last_name',
        'email',
        'state',
        'click_id',
        'sub_ids',
        'user_agent',
        'ip_address',
        'referring_url',
    ];

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(['date_start' => date('Y-m-d H:i:s'), 'date_end' => date('Y-m-d H:i:s', PHP_INT_MAX>>32)], true);
        parent::__construct($attributes);
    }
}
