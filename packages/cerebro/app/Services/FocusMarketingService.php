<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class FocusMarketingService
{

    private $url;
    private $unSoldApiKey;
    private $soldApiKey;
    private $creditCardsFMApiKEy;
    private array $postData;

    public function __construct()
    {
        $this->url = config('dnm.urls.focusmarketing');
        $this->soldApiKey = config('dnm.credentials.soldFMApiKey');
        $this->unSoldApiKey = config('dnm.credentials.unSoldFMApiKey');
        $this->creditCardsFMApiKEy = config('dnm.credentials.creditCardsFMApiKEy');
    }


    /**
     * @param $formData
     * @return mixed|void
     */
    public function sendRequest($formData, $decision)
    {
        if(in_array($decision['status'], array('sold', 'reject'))) {
            $this->prepareLeadData($formData, $decision);
        } else if($decision['status'] == 'creditCard') {
            $this->prepareCreditCardData($formData);
        } else {
            return "faild";
        }
        $ch = curl_init();
        if (!$ch) {
            die("Couldn't initialize a cURL handle");
        }
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        Log::channel('debugging')->info(json_encode($response));

        return json_decode($response,TRUE);
    }

    private function prepareLeadData($formData, $decision)
    {
        $this->postData['firstname'] = $formData->customer->first_name;
        $this->postData['lastname'] = $formData->customer->last_name;
        $this->postData['address1'] = $formData->customer->address;
        $this->postData['city'] = $formData->customer->city;
        $this->postData['state'] = $formData->customer->state;
        $this->postData['zip'] = $formData->customer->zip;
        $this->postData['phone1'] = $formData->customer->cell_phone;
        $this->postData['phone2'] = $formData->customer->home_phone;
        $this->postData['email'] = $formData->customer->email;
        $this->postData['DOB'] = $formData->customer->dob;
        $this->postData['LeadType'] = $formData->lead_type;
        $this->postData['NetIncome'] = $formData->customer->bank_info->net_month_income;
        $this->postData['PostURL'] = $formData->referring_url;
        $this->postData['LeadIP'] = $formData->customer->ip_address;
        $this->postData['InsertTime'] = $formData->updated_at->format('Y-m-d H:i:s');
        $this->postData['user_agent'] = $formData->user_agent;

        $this->postData['AffID'] = '';
        $sub_ids = json_decode($formData->sub_ids,true);
        if($sub_ids){
            $subId = $sub_ids['subId'] ?? null;
            $subId2 = $sub_ids['subId2'] ?? null;
            $subId3 = $sub_ids['subId3'] ?? null;
            $subId4 = $sub_ids['subId4'] ?? null;
            $subId5 = $sub_ids['subId5'] ?? null;
            $this->postData['AffID'] = $subId;
        }

        $this->postData['SoldAmount'] = $decision['price'] ?? 0;
        $this->postData['LoanAmount'] = $formData->requested_amount;

        if($decision['status'] == 'sold') {
            $this->postData['xAuthentication'] = $this->soldApiKey;
        } elseif($decision['status'] == 'reject') {
            $this->postData['xAuthentication'] = $this->unSoldApiKey;
        }
    }

    private function prepareCreditCardData($formData)
    {
        $this->postData['firstname'] = $formData->first_name;
        $this->postData['lastname'] = $formData->last_name;
        $this->postData['email'] = $formData->email;
        $this->postData['state'] = $formData->state;
        $this->postData['PostURL'] = $formData->referring_url;
        $this->postData['InsertTime'] = $formData->updated_at->format('Y-m-d H:i:s');
        $this->postData['user_agent'] = $formData->user_agent;
        $this->postData['ip'] = $formData->ip_address;

        $this->postData['xAuthentication'] = $this->creditCardsFMApiKEy;
    }

}
