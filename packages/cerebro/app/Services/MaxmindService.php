<?php

namespace App\Services;


use Illuminate\Support\Facades\Http;

class MaxmindService
{
    /**
     * @param $ip_address
     * @return mixed|void
     */
    static function checkIp($ip_address)
    {
        $user_id = config('services.maxmind.user_id');
        $license_key = config('services.maxmind.license_key');
        $url = config('services.maxmind.link');
        $is_test = config('services.maxmind.test');

        try{
            if($is_test){
                $response = Http::withBasicAuth($user_id, $license_key)->post($url,['device'=>['ip_address'=>config('services.maxmind.test_ip')]])->json();
            }else{
                $response = Http::withBasicAuth($user_id, $license_key)->post($url,['device'=>['ip_address'=>$ip_address]])->json();
            }
        } catch (\Exception $e) {
            return ['risk_processed' => 0, 'risk' => 0];
        }

        if(isset($response['warnings'])){
            foreach($response['warnings'] as $warning){
                if($warning['code'] == 'IP_ADDRESS_INVALID' || $warning['code'] == 'IP_ADDRESS_NOT_FOUND' || $warning['code'] == 'IP_ADDRESS_RESERVED'){
                    return ['risk_processed' => 1,'risk' => 100];
                }
            }
        }
        if(isset($response['ip_address']['risk'])){
            return ['risk_processed' => 1,'risk' => $response['ip_address']['risk']];
        }elseif(isset($response['risk_score'])){
            return ['risk_processed' => 1,'risk' => $response['risk_score']];
        }else{
            return ['risk_processed' => 1,'risk' => 100];
        }
    }

}
