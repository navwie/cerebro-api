<?php

namespace App\Models;

use App\Models\Traits\CanSaveQuietly;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class CustomerToBank extends Model implements Auditable
{
    use HasFactory, SoftDeletes, CanSaveQuietly;
    use \OwenIt\Auditing\Auditable;

    const TYPE_ACCOUNT = [
        'Checking',
        'Savings'
    ];
    protected $table = 'customers_to_banks';

    protected $fillable = [
        'bank_id',
        'customer_id',
        'account_type',
        'account_number',
        'bank_months',
        'net_month_income',
        'direct_deposit',
    ];

    protected $dates = [
        'deleted_at'
    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public function bank()
    {
        return $this->belongsTo('App\Models\Bank');
    }
}
