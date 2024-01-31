<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiReapplyImportResource extends JsonResource
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
                    'ssn' => $this->customer->ssn,
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
                    'accountnumber' => $this->customer->bank_info->account_number,
                    'bankmonths' => $this->customer->bank_info->bank_months,
                    'netmonthlyincome' => $this->customer->bank_info->net_month_income,
                    'directdeposit' => $this->customer->bank_info->direct_deposit ? 'Yes' : 'No',
                ],
            ],
        ];
    }
}
