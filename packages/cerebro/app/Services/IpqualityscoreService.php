<?php

namespace app\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IpqualityscoreService
{
    /**
     * @param $ip_address
     * @param $user_agent
     * @return mixed|void
     */
    static function checkIp($ip_address, $user_agent)
    {
        $key = config('services.ip_quality_score.auth_token');
        $parameters = [
            'user_agent' => $user_agent
        ];
        $formatted_parameters = http_build_query($parameters);
        $url = sprintf(
            config('services.ip_quality_score.ip_link') . '%s/%s?%s',
            $key,
            $ip_address,
            $formatted_parameters
        );

        $is_test = config('services.ip_quality_score.test');

        try {
            if ($is_test) {
                $response = [
                    'success' => true,
                    'fraud_score' => 0,
                ];
            } else {
                $response = Http::get($url)->json();
            }
        } catch (\Exception $e) {
            return ['risk_processed' => 0, 'risk' => 0];
        }

        if (isset($response['success']) && $response['success'] === true) {
            return ['risk_processed' => 1, 'risk' => $response['fraud_score']];
        } else {
            Log::channel('ip_quality_score')->notice(json_encode($response));
            return ['risk_processed' => 0, 'risk' => 0];
        }
    }
}
