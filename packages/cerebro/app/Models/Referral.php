<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Referral extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'referral';

    protected $fillable = [
        'aff_sub_id',
        'aff_sub_id2',
        'aff_sub_id3',
        'aff_sub_id4',
        'aff_sub_id5',
        'min_price',
        'referring_url',
        'channel_id',
        'password'
    ];

    protected $dates = [
        'deleted_at'
    ];

    public function reapply()
    {
        return $this->hasMany('App\Models\Reapply');
    }
}
