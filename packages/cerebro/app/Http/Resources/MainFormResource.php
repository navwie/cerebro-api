<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MainFormResource extends JsonResource
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
            'customerDataMain'=>[
                'reapply_id' => $this->id,
                'reason_for_loan' => ucwords($this->reason_for_loan, " "),
                'requested_amount' => $this->requested_amount,
                'email' => $this->customer->email,
                'debt_amount' => $this->customer->debt_amount,
                'specialist_talk' => (boolean)$this->specialist_talk,
                'first_name' => $this->customer->first_name,
                'last_name' => $this->customer->last_name,
                'dob' => date('m-d-Y', strtotime($this->customer->dob)),
                'address' => $this->customer->address,
                'address_month' => $this->customer->address_month,
                'armed_forces' => (boolean)$this->customer->armed_force,
                'citizenship' => (boolean)$this->customer->citizenship,
                'contact_time' => ucwords($this->customer->contact_time, " "),
                'city' => $this->customer->city,
                'credit_score' => $this->customer->credit_score,
                'dl_number' => $this->customer->dl_number,
                'dl_state' => $this->customer->dl_state,
                'emp_name' => $this->customer->emp_name,
                'emp_phone' => $this->customer->emp_phone,
                'emp_time' => $this->customer->emp_time,
                'home_phone' => $this->customer->home_phone,
                'income_type' => ucwords($this->customer->income_type, " "),
                'job_title' => $this->customer->job_title,
                'pay_frequency' => ucwords($this->pay_frequency, " "),
                'rent_or_own' => (boolean)$this->customer->rent_or_own,
                'own_car' => (boolean)$this->customer->own_car,
                'second_pay_day' => date('m-d-Y', strtotime($this->second_pay_day)),
                'next_pay_day' => date('m-d-Y', strtotime($this->next_pay_day)),
                'state' => $this->customer->state,
                'zip' => $this->customer->zip,
                'net_month_income' => $this->customer->bank_info->net_month_income,
                'submit_sms' => (boolean)$this->customer->submit_sms,
                'unsecureddebt' => (boolean)$this->customer->unsecureddebt,
            ],
        ];
    }
}
