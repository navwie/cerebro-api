<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DecisionAudit extends Model
{
    use HasFactory;

    protected $connection = 'mysql_audit';
    protected $table = 'decisions';
    protected $fillable = [
        'customer_id',
        'decision_id',
        'reapply_id',
        'referral_id',
        'decision_status',
        'decision_message',
        'decision_price',
        'decision_redirect',
        'redirected',
        'log_id'
    ];

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(['date_start' => date('Y-m-d H:i:s'), 'date_end' => date('Y-m-d H:i:s', PHP_INT_MAX>>32)], true);
        parent::__construct($attributes);
    }
}
