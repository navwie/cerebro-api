<?php

namespace App\Services;


use Illuminate\Support\Facades\Http;

class AbaService
{
    /**
     * @param $routing_number
     * @return \Illuminate\Http\Response
     */
    static function sendRequest($routing_number)
    {
        try{
            $url = config('services.aba.link');
            $response = Http::get($url,[
                'rn' => $routing_number
            ]);
            return json_decode($response,TRUE);
        } catch (\Exception $e) {
            return [
                "message" => "not found",
                "code" => 404
            ];
        }
    }
}
