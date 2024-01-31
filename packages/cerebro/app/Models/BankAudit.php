<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAudit extends Model
{
    protected $connection = 'mysql_audit';
    protected $table = 'banks';
    protected $fillable = ['bank_name', 'bank_phone', 'routing_number', 'log_id'];


    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(['date_start' => date('Y-m-d H:i:s'), 'date_end' => date('Y-m-d H:i:s', PHP_INT_MAX>>32)], true);
        parent::__construct($attributes);
    }
}
