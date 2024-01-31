<?php

namespace App\Rules;

use App\Services\LocationService;
use Illuminate\Contracts\Validation\Rule;

class Address implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @param string $zip
     * @return void
     */
    public function __construct($zip,$city,$state)
    {
        $this->message = '';
        $this->zip = $zip;
        $this->city = $city;
        $this->state = $state;
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
        $response = LocationService::sendRequest($value,$this->zip);
        if(!isset($response['Address']['Error'])){
            if($response['Address']['City'] == $this->city && $response['Address']['State'] == $this->state){
                return true;
            }
            $this->message = 'Data do not match.';
            return false;
        }else{
            $this->message = $response['Address']['Error']['Description'];
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
