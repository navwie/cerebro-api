<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Carbon\Carbon;
use App\Models\Traits\CanSaveQuietly;


class Reapply extends Model implements Auditable
{
    use HasFactory, SoftDeletes, CanSaveQuietly;
    use \OwenIt\Auditing\Auditable;

    const TYPE_REASONS_FOR_LOAN = [
        'Debt Consolidation',
        'Emergency Situation',
        'Auto Repair',
        'Auto Purchase',
        'Moving',
        'Home Improvement',
        'Medical',
        'Business',
        'Vacation',
        'Taxes',
        'Rent Or Mortgage',
        'Wedding',
        'Major Purchase',
        'Student Loan Refinance',
        'Credit Card Consolidation',
        'Other'
    ];

    const TYPE_PAY_FREQUENCY = [
        'Weekly',
        'Every 2 Weeks',
        'Twice A Month',
        'Monthly'
    ];

    protected $fillable = [
        'customer_id',
        'referral_id',
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
        'imported_mark',
        'flow_id',
        'action_type',
        'cookie_mark',
    ];

    protected $dates = [
        'deleted_at'
    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
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
