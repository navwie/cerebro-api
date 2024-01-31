<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClickCard extends Model
{
    use HasFactory;

    protected $table = 'clicks_to_card_site_items';

    protected $fillable = [
        'referral_id',
        'visitor_card_id',
        'card_site_item_id',
        'customer_credit_card_id',
        'click_amount',
        'date',
    ];
}
