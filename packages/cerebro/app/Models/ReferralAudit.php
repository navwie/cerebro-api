<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralAudit extends Model
{
    use HasFactory;
    protected $connection = 'mysql_audit';
    protected $table = 'referral';

    protected $fillable = [
        'log_id',
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

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(['date_start' => date('Y-m-d H:i:s'), 'date_end' => date('Y-m-d H:i:s', PHP_INT_MAX>>32)], true);
        parent::__construct($attributes);
    }
}
