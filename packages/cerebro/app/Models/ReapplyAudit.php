<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ReapplyAudit extends Model
{
    use HasFactory;

    protected $connection = 'mysql_audit';
    protected $table = 'reapplies';
    protected $fillable = [
        'log_id',
        'referral_id',
        'customer_id',
        'requested_amount',
        'lead_type',
        'reason_for_loan',
        'pay_frequency',
        'next_pay_day',
        'second_pay_day',
        'user_agent',
        'specialist_talk',
        'referring_url',
        'transaction_id',
        'click_id',
        'sub_ids',
        'risk',
        'risk_processed',
        'request_id',
        'mark_denied',
        'flow_id',
        'action_type',
        'cookie_mark',
        'request_id_mark',
    ];

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(['date_start' => date('Y-m-d H:i:s'), 'date_end' => date('Y-m-d H:i:s', PHP_INT_MAX>>32)], true);
        parent::__construct($attributes);
    }
    public function setNextPayDayAttribute($value)
    {
        try{
            $this->attributes['next_pay_day'] = Carbon::createFromFormat('m-d-Y', $value)->format('Y-m-d');
        } catch (\Exception $exception) {
            $this->attributes['next_pay_day'] = $value;
        }
    }

    public function setSecondPayDayAttribute($value)
    {
        try {
            $this->attributes['second_pay_day'] = Carbon::createFromFormat('m-d-Y', $value)->format('Y-m-d');
        } catch (\Exception $exception) {
            $this->attributes['second_pay_day'] = $value;
        }
    }
}
