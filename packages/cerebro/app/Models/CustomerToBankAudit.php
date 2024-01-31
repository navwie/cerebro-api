<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerToBankAudit extends Model
{
    protected $connection = 'mysql_audit';
    protected $table = 'customers_to_banks';
    protected $fillable = [
        'log_id',
        'bank_id',
        'customer_id',
        'account_type',
        'account_number',
        'routing_number',
        'bank_months',
        'net_month_income',
        'direct_deposit',
    ];

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(['date_start' => date('Y-m-d H:i:s'), 'date_end' => date('Y-m-d H:i:s', PHP_INT_MAX>>32)], true);
        parent::__construct($attributes);
    }
}
