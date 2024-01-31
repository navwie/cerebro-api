<?php

namespace App\Services;


use Illuminate\Support\Facades\Http;

class ZipService
{
    /**
     * @param  $address
     * @param $zip
     * @return \Illuminate\Http\Response
     */
    static function sendRequest($zip)
    {
        $url = config('services.locationApi.link');

        $xml = '<CityStateLookupRequest USERID="027DNM004981">' .
            '<ZipCode ID="0">' .
            '<Zip5>' . $zip . '</Zip5>' .
            '</ZipCode>' .
            '</CityStateLookupRequest>';
        $response = Http::get($url,[
            'API' => 'CityStateLookup',
            'XML' => $xml
        ]);
        $xmlResponse = simplexml_load_string($response);
        $json = json_encode($xmlResponse);
        return json_decode($json,TRUE);
    }
}
