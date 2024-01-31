<?php

namespace App\Rules;

use App\Models\ZipCode;
use Illuminate\Contracts\Validation\Rule;

class UsaZip implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->message = '';
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
        $zipCode = ZipCode::where('zip_code',$value)->first();

        if ($zipCode) {
            return true;
        }

        $this->message = 'ZIP Code not found in the internal database';
        return false;

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
