<?php

namespace App\Rules;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\RestException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Redis;

class UsaPhone implements Rule
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
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $key = config('services.ip_quality_score.auth_token');
        $countries = ['US'];
        $parameters = [
            'country' => $countries
        ];
        $formatted_parameters = http_build_query($parameters);
        $url = sprintf(
            config('services.ip_quality_score.phone_link') . '%s/%s?%s',
            $key,
            $value,
            $formatted_parameters
        );

        if (Redis::get($value) != null) {
            $this->message = 'Is not valid USA phone.';
            return false;
        }

        try {
            $response = Http::get($url)->json();
            if (isset($response['success']) && $response['success'] === true) {
                if(!$response['valid']){
                    Redis::set($value, 'not valid', 'EX', 3600);
                    $this->message = 'Is not valid USA phone.';
                }
                return $response['valid'];
            } else {
                Log::channel('ip_quality_score')->notice(json_encode($response));
                return true;
            }
        } catch (RestException $e) {
            $this->message = 'Is not valid USA phone.';
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
