<?php

namespace App\Models;

use App\Models\Traits\CanSaveQuietly;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Bank extends Model implements Auditable
{
    use HasFactory;
    use AuditableTrait;
    use CanSaveQuietly;


    protected $fillable = ['bank_name', 'bank_phone','routing_number'];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public function bank_info()
    {
        return $this->belongsTo('App\Models\Bank');
    }
}
