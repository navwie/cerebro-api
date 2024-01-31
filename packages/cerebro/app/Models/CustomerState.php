<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CustomerState extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'referral_id',
        'referral_url',
        'click_id',
        'state',
        'token',
    ];

    public function get_customer_state(Request $request)
    {
         $state = CustomerState::where([
            'token' => $request->key,
        ])->first();

         if($state){
             return response()->json(openssl_decrypt($state->state,'aes-128-cbc',config('hashing.hashing_key'),0,config('hashing.hashing_iv')), 200);
         }

        return response()->json('Not found', 200);
    }
}
