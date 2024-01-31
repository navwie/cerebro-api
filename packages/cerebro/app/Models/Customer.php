<?php

namespace App\Models;

use App\Models\Traits\CanSaveQuietly;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Carbon\Carbon;

class Customer extends Model implements Auditable
{
    use HasFactory, SoftDeletes, CanSaveQuietly;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'customers';

    const TYPE_CONTACT_TIME = [
        'Anytime',
        'Morning',
        'Afternoon',
        'Evening'
    ];

    const TYPE_INCOME_PERSONAL = [
        'Employed',
        'Self Employed',
        'Pension',
        'Social Security',
        'Disability',
        'Military',
        'Other'
    ];

    const TYPE_INCOME_LEAD = [
        'Job Income',
        'Self Employed',
        'Benefits',
        'Pension',
        'Social Security',
        'Disability Income',
        'Military',
        'Other'
    ];

    const TYPE_INCOME_PERSONAL_TO_LEAD = [
        'Employed' => 'Job Income',
        'Self Employed' => 'Self Employed',
        'Pension' => 'Pension',
        'Social Security' => 'Social Security',
        'Disability' => 'Disability Income',
        'Military' => 'Military',
        'Other' => 'Other'
    ];

    const TYPE_INCOME_LEAD_TO_PERSONAL = [
        'Job Income' => 'Employed',
        'Self Employed' => 'Self Employed',
        'Pension' => 'Pension',
        'Social Security' => 'Social Security',
        'Disability Income' => 'Disability',
        'Military' => 'Military',
        'Benefits' => 'Other',
        'Other' => 'Other'
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'ip_address',
        'ssn',
        'ssn_short',
        'email',
        'dob',
        'first_name',
        'last_name',
        'address',
        'state',
        'city',
        'zip',
        'home_phone',
        'cell_phone',
        'dl_state',
        'dl_number',
        'armed_forces',
        'rent_or_own',
        'citizenship',
        'emp_time',
        'emp_name',
        'emp_phone',
        'job_title',
        'contact_time',
        'address_month',
        'own_car',
        'income_type',
        'credit_score',
        'debt_amount',
        'submit_sms',
        'unsecureddebt'
    ];

    public function reapply()
    {
        return $this->hasMany('App\Models\Reapply');
    }

    public function bank_info()
    {
        return $this->hasOne('App\Models\CustomerToBank', 'customer_id', 'id');
    }

    public function decision()
    {
        return $this->hasOne('App\Models\Decisions', 'customer_id', 'id');
    }

    public function setDobAttribute($value)
    {
        try {
            $this->attributes['dob'] = Carbon::createFromFormat('m-d-Y', $value)->format('Y-m-d');
        } catch (\Exception $exception) {
            $this->attributes['dob'] = $value;
        }
    }
}
