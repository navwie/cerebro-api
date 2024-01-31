<?php

namespace App\Services;


use Illuminate\Support\Facades\Http;

class LocationService
{
    /**
     * @param  $address
     * @param $zip
     * @return \Illuminate\Http\Response
     */
    static function sendRequest($address,$zip)
    {
        $url = config('services.locationApi.link');
        $xml = '<AddressValidateRequest USERID="027DNM004981">' .
            '<Revision>1</Revision>' .
            '<Address ID="0">' .
            '<Address1></Address1>' .
            '<Address2>' . $address . '</Address2>' .
            '<City></City>' .
            '<State></State>' .
            '<Zip5>' . $zip . '</Zip5>' .
            '<Zip4></Zip4>' .
            '</Address>' .
            '</AddressValidateRequest>';
        try{
            $response = Http::get($url,[
                'API' => 'Verify',
                'XML' => $xml
            ]);
            $xmlResponse = simplexml_load_string($response);
            $json = json_encode($xmlResponse);
            return json_decode($json,TRUE);
        } catch (\Exception $e) {
           return ['Address'=>[
                'Error'=>[
                    'Description'=>'Address Not Found.'
                ]
            ]];
        }
    }
}
