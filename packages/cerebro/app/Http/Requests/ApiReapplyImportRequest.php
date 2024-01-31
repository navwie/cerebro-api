<?php

namespace App\Http\Requests;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Reapply;
use App\Models\Customer;
use App\Models\CustomerToBank;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ApiReapplyImportRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'requested_amount' => ['required', 'numeric'],
            'reason_for_loan' => [
                'required',
                'string',
                'in:' . implode(',', Reapply::TYPE_REASONS_FOR_LOAN)
            ],
            'pay_frequency' => [
                'required',
                'string',
                'in:' . implode(',', Reapply::TYPE_PAY_FREQUENCY)
            ],
            'credit_score' => ['required', 'numeric', 'max:999', 'min:0'],
            'ip_address' => ['required', 'ip'],
            'ssn' => ['required', 'numeric', 'digits:9'],
            'dob' => ['required', 'date_format:m-d-Y', 'before:'.\Carbon\Carbon::now()->subYears(18)->format('Y-m-d'), 'after:'.\Carbon\Carbon::now()->subYears(100)->format('Y-m-d')],
            'first_name' => ['required', 'alpha', 'min:2', 'max:128'],
            'last_name' => ['required', 'alpha', 'min:2', 'max:128'],
            'address' => ['required', 'string', 'min:5', 'max:128'],
            'city' => ['required', 'string', 'min:2', 'max:128'],
            'state' => ['required', 'string', 'size:2'],
            'zip' => [
                'required',
                'size:5',
                ],
            'address_month' => ['required', 'numeric', 'min:0', 'max:860'],
            'rent_or_own' => ['required', 'boolean'],
            'email' => ['required', 'email:rfc,dns'],
            'home_phone' => ['required', 'digits:10','regex:/^[2-9].*/'],
            'dl_state' => ['required', 'string', 'size:2'],
            'dl_number' => ['required'],
            'citizenship' => ['required', 'boolean'],
            'armed_forces' => ['required', 'boolean'],
            'contact_time' => ['required', 'in:' . implode(',', Customer::TYPE_CONTACT_TIME)],
            'income_type' => ['required', 'in:' . implode(',', array_merge_recursive(Customer::TYPE_INCOME_LEAD,Customer::TYPE_INCOME_PERSONAL))],
            'emp_time' => ['required', 'numeric', 'min:0', 'max:860'],
            'emp_name' => ['required', 'string', 'min:2', 'max:128'],
            'emp_phone' => ['required', 'digits:10'],
            'job_title' => ['required', 'string', 'min:2', 'max:128'],
            'next_pay_day' => ['required', 'date_format:m-d-Y'],
            'second_pay_day' => ['required', 'date_format:m-d-Y', 'after:next_pay_day'],
            'bank_name' => ['required', 'string', 'min:2', 'max:128'],
            'bank_phone' => ['required', 'string', 'digits:10'],
            'account_type' => ['required','in:' . implode(',', CustomerToBank::TYPE_ACCOUNT)],
            'routing_number' => ['required', 'digits:9'],
            'account_number' => ['required', 'digits_between:4,30'],
            'bank_months' => ['required','numeric', 'min:0', 'max:860'],
            'net_month_income' => ['required', 'numeric'],
            'direct_deposit' => ['required', 'boolean'],
            'own_car' => ['required', 'boolean'],
            'aff_sub_id' => ['string', 'max:65'],
            'aff_sub_id2' => ['string', 'max:65'],
            'aff_sub_id3' => ['string', 'max:65'],
            'user_agent' => ['string'],
            'decision_id' => ['required', 'numeric'],
            'decision_status' => ['required', 'string'],
            'decision_message' => ['required', 'string'],
            'decision_price' => ['required', 'numeric'],
            'decision_redirect' => ['required', 'string'],
            'source_type' => ['required', 'in:'.User::TYPE_SOURCE_LEAD . ',' . User::TYPE_SOURCE_PERSONAL],
            'channel_id' => ['required'],
            'password' => ['required'],
        ];
    }

    function prepareForValidation()
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->getContent());
        if($xml) {
            $json = json_encode($xml);
            $data = json_decode($json, true);
            $this->array_change_key_case_recursive($data);
            $data = array_change_key_case($data, CASE_LOWER);
            $source = array_change_key_case($data['source'], CASE_LOWER);
            $customet = array_change_key_case($data['customer'], CASE_LOWER);
            $personal = array_change_key_case($customet['personal'], CASE_LOWER);
            $employment = array_change_key_case($customet['employment'], CASE_LOWER);
            $bank = array_change_key_case($customet['bank'], CASE_LOWER);
            $decision = array_change_key_case($data['decision'], CASE_LOWER);
            $result = [
                //source
                'source_name' => $source['sourcename'] ?? '',
                'source_email' => $source['sourceemail'] ?? '',
                'source_type' => $source['sourcetype'] ?? '',
                'channel_id' => $source['channelid'] ?? '',
                'password' => $source['password'] ?? '',

                //personal
                'ip_address' => $personal['ipaddress'] ?? '',
                'ssn' => $personal['ssn'] ?? '',
                'dob' => $personal['dob'] ?? '',
                'first_name' => $personal['firstname'] ?? '',
                'last_name' => $personal['lastname'] ?? '',
                'address' => $personal['address'] ?? '',
                'city' => $personal['city'] ?? '',
                'state' => $personal['state'] ?? '',
                'zip' => $personal['zip'] ?? '',
                'home_phone' => $personal['homephone'] ?? '',
                'dl_state' => $personal['dlstate'] ?? '',
                'dl_number' => $personal['dlnumber'] ?? '',
                'armed_forces' => $personal['armedforces'] ?? 0,
                'contact_time' => $personal['contacttime'] ?? '',
                'rent_or_own' => $personal['rentorown'] ?? 0,
                'email' => $personal['email'] ?? '',
                'address_month' => $personal['addressmonth'] ?? '',
                'citizenship' => $personal['citizenship'] ?? 0,
                'own_car' => $personal['owncar'] ?? 0,
                'user_agent' => $personal['useragent'] ?? '',

                //employment
                'income_type' => $employment['incometype'] ?? '',
                'emp_time' => $employment['emptime'] ?? '',
                'emp_name' => $employment['empname'] ?? '',
                'emp_phone' => $employment['empphone'] ?? '',
                'job_title' => $employment['jobtitle'] ?? '',
                'pay_frequency' => $employment['payfrequency'] ?? '',
                'next_pay_day' => $employment['nextpaydate'] ?? '',
                'second_pay_day' => $employment['secondpaydate'] ?? '',

                //bank
                'bank_name' => $bank['bankname'] ?? '',
                'bank_phone' => $bank['bankphone'] ?? '',
                'account_type' => $bank['accounttype'] ?? '',
                'routing_number' => $bank['routingnumber'] ?? '',
                'account_number' => $bank['accountnumber'] ?? '',
                'bank_months' => $bank['bankmonths'] ?? '',
                'net_month_income' => $bank['netmonthlyincome'] ?? '',
                'direct_deposit' => $bank['directdeposit'] ?? 0,

                //decision
                'decision_id' => $decision['decisionid'] ?? '',
                'decision_status' => $decision['decisionstatus'] ?? '',
                'decision_message' => $decision['decisionmessage'] ?? '',
                'decision_price' => $decision['decisionprice'] ?? '',
                'decision_redirect' => $decision['decisionredirect'] ?? '',
            ];

            if(!empty($customet['loan']))
            {
                $loan = array_change_key_case($customet['loan'], CASE_LOWER);
                //loan
                $result['reason_for_loan'] = isset($loan['reasonforloan']) ? $loan['reasonforloan'] ?? '' : '';
                $result['requested_amount'] = isset($loan['loanamount']) ? $loan['loanamount'] ?? '' : '';
                $result['credit_score'] = isset($loan['creditscore']) ? $loan['creditscore'] ?? '' : '';
            } else {
                $result['requested_amount'] = isset($personal['requestedamount']) ? $personal['requestedamount'] ?? 0 : 0;
            }
            if($result['credit_score'] == 0)
            {
                $result['credit_score'] = 650;
            }
            $this->merge($result);
        }
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        Log::channel('import_validation_errors')->error($this->validator->errors());
        Log::channel('import_validation_errors')->info($this->getContent());
        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }

    function array_change_key_case_recursive($arr, $case = CASE_LOWER)
    {
        return array_map(function($item) use($case) {
            if(is_array($item))
                $item = $this->array_change_key_case_recursive($item, $case);
            return $item;
        },array_change_key_case($arr, $case));
    }
}
