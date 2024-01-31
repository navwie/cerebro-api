<?php

namespace App\Rules;

use App\Models\Customer;
use App\Models\Reapply;
use Illuminate\Contracts\Validation\Rule;

class ssnAuth implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @param string $reapply_id
     * @return void
     */
    public function __construct($reapply_id)
    {
        $this->reapply_id = $reapply_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $reapply = Reapply::find($this->reapply_id);
        $customer = Customer::find($reapply->customer_id);
        if(openssl_decrypt($customer->ssn,'aes-128-cbc',config('hashing.hashing_key'),0,config('hashing.hashing_iv')) == $value){
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "Not correct SSN";
    }
}
