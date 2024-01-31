<?php

namespace App\Rules;

use App\Services\AbaService;
use Illuminate\Contracts\Validation\Rule;

class Aba implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($bank_name,$bank_phone)
    {
        $this->message = '';
        $this->bank_name = $bank_name;
        $this->bank_phone = $bank_phone;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $response = AbaService::sendRequest($value);
        if($response['code'] == 200){
            if($response['customer_name'] == $this->bank_name && str_replace('-', '', $response['telephone']) == $this->bank_phone){
                return true;
            }
            $this->message = 'Data do not match.';
            return false;
        }else{
            $this->message = $response['message'];
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
