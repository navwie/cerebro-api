<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiReapplyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $sub_ids = json_decode($this->sub_ids,true);
        if($sub_ids){
            $subId = $sub_ids['subId'] ?? null;
            $subId2 = $sub_ids['subId2'] ?? null;
            $subId3 = $sub_ids['subId3'] ?? null;
            $subId4 = $sub_ids['subId4'] ?? null;
            $subId5 = $sub_ids['subId5'] ?? null;
        }
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
                    'loanamount' => $this->requested_amount,
                    'reasonforloan' => $this->reason_for_loan,
                    'creditscore' => $this->customer->credit_score,
                ],
                'personal' => [
                    'ipaddress' => $this->customer->ip_address,
                    'requestedamount' => $this->requested_amount,
                    'reasonforloan' => $this->reason_for_loan,
                    'ssn' => openssl_decrypt($this->customer->ssn,'aes-128-cbc',config('hashing.hashing_key'),0,config('hashing.hashing_iv')),
                    'dob' => date('m/d/Y', strtotime($this->customer->dob)),
                    'firstname' => $this->customer->first_name,
                    'lastname' => $this->customer->last_name,
                    'address' => $this->customer->address,
                    'city' => $this->customer->city,
                    'state' => $this->customer->state,
                    'zip' => $this->customer->zip,
                    'homephone' => $this->customer->home_phone,
                    'cellphone' => $this->customer->cell_phone,
                    'dlstate' => $this->customer->dl_state,
                    'dlnumber' => $this->customer->dl_number,
                    'armedforces'  => $this->customer->armed_forces ? 'Yes' : 'No',
                    'contacttime' => $this->customer->contact_time,
                    'rentorown' => $this->customer->rent_or_own ? 'rent' : 'own',
                    'email' => $this->customer->email,
                    'addressmonth' => $this->customer->address_month,
                    'citizenship' => $this->customer->citizenship ? 'Yes' : 'No',
                    'owncar' => $this->customer->own_car ? 'Yes' : 'No',
                    'specialisttalk'  => $this->specialist_talk ? 'Yes' : 'No',
                    'useragent' => $this->user_agent,
                    'totaldebtamount' => $this->customer->debt_amount
                ],
                'employment' => [
                    'incometype' => $this->customer->income_type,
                    'emptime' => $this->customer->emp_time,
                    'empname' => $this->customer->emp_name,
                    'empphone' => $this->customer->emp_phone,
                    'jobtitle' => $this->customer->job_title,
                    'payfrequency' => $this->pay_frequency,
                    'nextpaydate' => date('m/d/Y', strtotime($this->next_pay_day)),
                    'secondpaydate' => date('m/d/Y', strtotime($this->second_pay_day)),
                ],
                'bank' => [
                    'bankname' => $this->customer->bank_info->bank->bank_name,
                    'bankphone' => $this->customer->bank_info->bank->bank_phone,
                    'accounttype' => $this->customer->bank_info->account_type,
                    'routingnumber' => $this->customer->bank_info->bank->routing_number,
                    'accountnumber' => openssl_decrypt($this->customer->bank_info->account_number,'aes-128-cbc',config('hashing.hashing_key'),0,config('hashing.hashing_iv')),
                    'bankmonths' => $this->customer->bank_info->bank_months,
                    'netmonthlyincome' => $this->customer->bank_info->net_month_income,
                    'monthlyincome' => $this->customer->bank_info->net_month_income,
                    'directdeposit' => $this->customer->bank_info->direct_deposit ? 'Yes' : 'No',
                ],
            ],
        ];
    }
}
