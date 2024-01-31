<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReapplyFormResource extends JsonResource
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
            'customerDataReapply'=>[
                'customer_id' => $this->customer->id,
                'credit_score' => $this->customer->credit_score,
                'email' => $this->customer->email,
                'first_name' => $this->customer->first_name,
                'job_title' => $this->customer->job_title,
                'second_pay_day' => date('m-d-Y', strtotime($this->second_pay_day)),
                'next_pay_day' => date('m-d-Y', strtotime($this->next_pay_day)),
                'net_month_income' => $this->customer->bank_info->net_month_income,
                'pay_frequency' => ucwords($this->pay_frequency, " "),
                'phone' => $this->customer->home_phone,
                'debt_amount' => $this->customer->debt_amount,
                'imported_mark' => $this->imported_mark,
            ],
        ];
    }
}
