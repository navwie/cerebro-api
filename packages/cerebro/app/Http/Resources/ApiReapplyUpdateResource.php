<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiReapplyUpdateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'referral' => [
                'channelId' => $this->channel_id,
                'password' => $this->password,
                'minPrice' => $this->min_price,
                'affSubId' => $subId ?? '',
                'affSubId2' => $subId2 ?? '',
                'affSubId3' => $subId3 ?? '',
                'affSubId4' => $subId4 ?? '',
                'affSubId5' => $subId5 ?? '',
                'referringurl' => $this->referring_url,
            ],
            'customer' => [
                'customer_id' => $this->customer->id,
                'loan' => [
                    'creditscore' => $this->customer->credit_score,
                ],
                'personal' => [
                    'requestedamount' => $this->requested_amount,
                    'firstname' => $this->customer->first_name,
                    'lastname' => $this->customer->last_name,
                    'email' => $this->customer->email,
                ],
                'employment' => [
                    'jobtitle' => $this->customer->job_title,
                    'payfrequency' => $this->pay_frequency,
                    'nextpaydate' => date('m-d-Y', strtotime($this->next_pay_day)),
                    'secondpaydate' => date('m-d-Y', strtotime($this->second_pay_day))
                ],
                'bank' => [
                    'netmonthlyincome' => $this->customer->bank_info->net_month_income
                ],
            ],
        ];
    }
}
