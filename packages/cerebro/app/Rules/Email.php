<?php

namespace app\Rules;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Twilio\Rest\Client;
use Twilio\Exceptions\RestException;
use Illuminate\Contracts\Validation\Rule;

class Email implements Rule
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

        $url = sprintf(
            config('services.ip_quality_score.email_link') . '%s/%s',
            $key,
            urlencode($value)
        );

        if (Redis::get($value) != null) {
            $this->message = 'Is not valid email.';
            return false;
        }

        try {
            $response = Http::get($url)->json();
            if (isset($response['success']) && $response['success'] === true) {
                if(!$response['valid']){
                    Redis::set($value, 'not valid', 'EX', 3600);
                    $this->message = 'Is not valid email.';
                }
                return $response['valid'];
            } else {
                Log::channel('ip_quality_score')->notice(json_encode($response));
                return true;
            }
        } catch (RestException $e) {
            $this->message = 'Is not valid email.';
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
