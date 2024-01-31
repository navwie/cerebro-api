<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Testing\Fluent\Concerns\Has;

class CustomerAudit extends Model
{
    use HasFactory;
    protected $connection = 'mysql_audit';
    protected $table = 'customers';
    protected $fillable = [
        'log_id',
        'ip_address',
        'ssn',
        'ssn_short',
        'dob',
        'first_name',
        'last_name',
        'address',
        'email',
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
        'debt_amount',
        'submit_sms',
        'unsecureddebt',
        'credit_score'
    ];


    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(['date_start' => date('Y-m-d H:i:s'), 'date_end' => date('Y-m-d H:i:s', PHP_INT_MAX>>32)], true);
        parent::__construct($attributes);
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
