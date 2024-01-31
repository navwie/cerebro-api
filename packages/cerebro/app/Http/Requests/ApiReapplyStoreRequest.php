<?php

namespace App\Http\Requests;


use App\Models\User;
use App\Rules\Email;
use App\Rules\UsaPhone;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Reapply;
use App\Models\Customer;
use App\Models\CustomerToBank;
use App\Rules\Aba;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

class ApiReapplyStoreRequest extends FormRequest
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
        $validations = [
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
            'credit_score' => ['required', 'numeric', 'min:0', 'max:999'],
            'ssn_non_encrypted' => ['required', 'numeric', 'digits:9'],
            'ssn' => ['required', 'unique:customers'],
            'dob' => ['required', 'date_format:m-d-Y', 'before:'.\Carbon\Carbon::now()->subYears(18)->format('Y-m-d'), 'after:'.\Carbon\Carbon::now()->subYears(100)->format('Y-m-d')],
            'first_name' => ['required', 'string', 'min:2', 'max:128'],
            'last_name' => ['required', 'string','min:2', 'max:128'],
            'address' => ['required', 'string', 'min:5', 'max:128'],
            'city' => ['required', 'string', 'min:2', 'max:128'],
            'state' => ['required', 'string', 'size:2'],
            'submit_sms' => ['required','boolean'],
            'zip' => [
                'required',
                'size:5'
            ],
            'address_month' => ['required', 'numeric', 'min:0', 'max:860'],
            'rent_or_own' => ['required', 'boolean'],
            'email' => ['required', 'unique:customers', 'email:rfc,dns', new Email()],
            'home_phone' => ['required', 'digits:10','regex:/^[02-9].*/', new UsaPhone()],
            'dl_state' => ['required', 'string', 'size:2'],
            'dl_number' => ['required','regex:/^[\w-]*$/'],
            'citizenship' => ['required', 'boolean'],
            'armed_forces' => ['required', 'boolean'],
            'contact_time' => ['required', 'in:' . implode(',', Customer::TYPE_CONTACT_TIME)],
            'income_type' => ['required', 'in:' . implode(',', array_merge_recursive(Customer::TYPE_INCOME_LEAD,Customer::TYPE_INCOME_PERSONAL))],
            'emp_time' => ['required', 'numeric', 'min:0', 'max:860'],
            'emp_name' => ['required', 'string', 'min:2', 'max:128'],
            'emp_phone' => ['required', 'digits:10','regex:/^[02-9].*/', new UsaPhone()],
            'job_title' => ['required', 'string', 'min:2', 'max:128'],
            'next_pay_day' => ['required', 'date_format:m-d-Y', 'after:yesterday', 'before:'.\Carbon\Carbon::now()->addYear()->format('Y-m-d')],
            'second_pay_day' => ['required', 'date_format:m-d-Y', 'after:next_pay_day', 'before:'.\Carbon\Carbon::now()->addYear()->format('Y-m-d')],
            'bank_name' => ['required', 'string', 'min:2', 'max:128'],
            'bank_phone' => ['required', 'string', 'digits:10'],
            'account_type' => ['required','in:' . implode(',', CustomerToBank::TYPE_ACCOUNT)],
            'routing_number' => ['required', 'digits:9',new Aba($this->input('bank_name'),$this->input('bank_phone'))],
            'account_number_non_encrypted' => ['required', 'digits_between:4,30'],
            'bank_months' => ['required','numeric', 'min:0', 'max:860'],
            'net_month_income' => ['required', 'numeric'],
            'direct_deposit' => ['required', 'boolean'],
            'debt_amount' => ['required', 'numeric'],
            'own_car' => ['required', 'boolean'],
            'specialist_talk' => ['required', 'boolean'],
            'aff_sub_id' => ['string', 'max:65'],
            'aff_sub_id2' => ['string', 'max:65'],
            'aff_sub_id3' => ['string', 'max:65'],
            'user_agent' => ['string'],
            'referring_url' => ['string', 'min:12', 'max:256'],
            'lead_type' => [
                'required',
                'string',
                'in:' . implode(',', [User::TYPE_SOURCE_LEAD , User::TYPE_SOURCE_PERSONAL]),
            ],
            'auto_redirect_to_lead' => [
                'required',
                'boolean'
            ],
        ];
        if (Route::getCurrentRoute()->getActionMethod() == 'store') {
            $validations['request_id'] = ['required', 'unique:App\Models\ReapplyAudit'];
        }
        return $validations;
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
        $failedFields = $validator->failed();
        if (isset($failedFields['request_id'])) {
            Log::channel('validate_request_id')->error($this->validator->errors() . 'time: ' . date('m/d/Y h:i:s a', time()) . ' | content: ' . $this->getContent());
        }
        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }

    function prepareForValidation()
    {
        $this->merge([
            'ssn' => openssl_encrypt($this->ssn,'aes-128-cbc',config('hashing.hashing_key'),0,config('hashing.hashing_iv')),
            'ssn_short' => openssl_encrypt(substr($this->ssn, -4),'aes-128-cbc',config('hashing.hashing_key'),0,config('hashing.hashing_iv')),
            'ssn_non_encrypted' => $this->ssn,
            'account_number' => openssl_encrypt($this->account_number,'aes-128-cbc',config('hashing.hashing_key'),0,config('hashing.hashing_iv')),
            'account_number_non_encrypted' => $this->account_number,
            'sub_ids' => json_encode($this->sub_ids, true, JSON_THROW_ON_ERROR),
        ]);
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->getContent());
        if($xml) {
            $json = json_encode($xml);
            $data = json_decode($json, true);
            $this->array_change_key_case_recursive($data);
            $data = array_change_key_case($data, CASE_LOWER);
            $customet = array_change_key_case($data['customer'], CASE_LOWER);
            $personal = array_change_key_case($customet['personal'], CASE_LOWER);
            $employment = array_change_key_case($customet['employment'], CASE_LOWER);
            $bank = array_change_key_case($customet['bank'], CASE_LOWER);
            $referral = array_change_key_case($data['referral'], CASE_LOWER);

            $result = [
                //personal
                'ip_address' => !is_array($personal['ipaddress']) ? $personal['ipaddress'] ?? '' : '',
                'ssn' => !is_array($personal['ssn']) ? $personal['ssn'] ?? '' : '',
                'dob' => !is_array($personal['dob']) ? $personal['dob'] ?? '' : '',
                'first_name' => !is_array($personal['firstname']) ? $personal['firstname'] ?? '' : '',
                'last_name' => !is_array($personal['lastname']) ? $personal['lastname'] ?? '' : '',
                'address' => !is_array($personal['address']) ? $personal['address'] ?? '' : '',
                'city' => !is_array($personal['city']) ? $personal['city'] ?? '' : '',
                'state' => !is_array($personal['state']) ? $personal['state'] ?? '' : '',
                'zip' => !is_array($personal['zip']) ? $personal['zip'] ?? '' : '',
                'home_phone' => !is_array($personal['homephone']) ? $personal['homephone'] ?? '' : '',
                'cell_phone' => !is_array($personal['cellphone']) ? $personal['cellphone'] ?? '' : '',
                'dl_state' => !is_array($personal['dlstate']) ? $personal['dlstate'] ?? '' : '',
                'dl_number' => !is_array($personal['dlnumber']) ? $personal['dlnumber'] ?? '' : '',
                'armed_forces' => $personal['armedforces'] == 'Yes' ? 1 : 0,
                'contact_time' => !is_array($personal['contacttime']) ? $personal['contacttime'] ?? '' : '',
                'rent_or_own' => $personal['rentorown'] == 'Own' ? 1 : 0,
                'email' => !is_array($personal['email']) ? $personal['email'] ?? '' : '',
                'address_month' => !is_array($personal['addressmonth']) ? $personal['addressmonth'] ?? '' : '',
                'citizenship' => isset($referral['citizenship']) ? $personal['citizenship'] ? 1 : 0 : 0,
                'own_car' => !is_array($personal['owncar']) ? $personal['owncar'] ?? 0 : 0,
                'user_agent' => !is_array($personal['useragent']) ? $personal['useragent'] ?? '' : '',

                //employment
                'income_type' => !is_array($employment['incometype']) ? $employment['incometype'] ?? '' : '',
                'emp_time' => !is_array($employment['emptime']) ? $employment['emptime'] ?? '' : '',
                'emp_name' => !is_array($employment['empname']) ? $employment['empname'] ?? '' : '',
                'emp_phone' => !is_array($employment['empphone']) ? $employment['empphone'] ?? '' : '',
                'job_title' => !is_array($employment['jobtitle']) ? $employment['jobtitle'] ?? '' : '',
                'pay_frequency' => !is_array($employment['payfrequency']) ? $employment['payfrequency'] ?? '' : '',
                'next_pay_day' => !is_array($employment['nextpaydate']) ? $employment['nextpaydate'] ?? '' : '',
                'second_pay_day' => !is_array($employment['secondpaydate']) ? $employment['secondpaydate'] ?? '' : '',

                //bank
                'bank_name' => !is_array($bank['bankname']) ? $bank['bankname'] ?? '' : '',
                'bank_phone' => !is_array($bank['bankphone']) ? $bank['bankphone'] ?? '' : '',
                'account_type' => !is_array($bank['accounttype']) ? $bank['accounttype'] ?? '' : '',
                'routing_number' => !is_array($bank['routingnumber']) ? $bank['routingnumber'] ?? '' : '',
                'account_number' => !is_array($bank['accountnumber']) ? $bank['accountnumber'] ?? '' : '',
                'bank_months' => !is_array($bank['bankmonths']) ? $bank['bankmonths'] ?? '' : '',
                'net_month_income' => !is_array($bank['netmonthlyincome']) ? $bank['netmonthlyincome'] ?? '' : '',
                'direct_deposit' => $bank['directdeposit'] == 'YES' ? 1 : 0,

                //referral
                'aff_sub_id' => isset($referral['affSubId']) ? !is_array($referral['affSubId']) ? $referral['affSubId'] ?? '' : '': '',
                'aff_sub_id2' => isset($referral['affSubId2']) ? !is_array($referral['affSubId2']) ? $referral['affSubId2'] ?? '' : '' : '',
                'aff_sub_id3' => isset($referral['affSubId3']) ? !is_array($referral['affSubId3']) ? $referral['affSubId3'] ?? '' : '' : '',
                'aff_sub_id4' => isset($referral['affSubId4']) ? !is_array($referral['affSubId4']) ? $referral['affSubId4'] ?? '' : '' : '',
                'aff_sub_id5' => isset($referral['affSubId5']) ? !is_array($referral['affSubId5']) ? $referral['affSubId5'] ?? '' : '' : '',
                'referring_url' => !is_array($referral['referringurl']) ? $referral['referringurl'] ?? '' : '',
                'channel_id' => !is_array($referral['channelid']) ? $referral['channelid'] ?? '' : '',
                'password' => !is_array($referral['password']) ? $referral['password'] ?? '' : '',
                'min_price' => !is_array($referral['minprice']) ? $referral['minprice'] ?? '' : '',
            ];

            if(!empty($customet['loan']))
            {
                $loan = array_change_key_case($customet['loan'], CASE_LOWER);
                //loan
                $result['reason_for_loan'] = !is_array($loan['reasonforloan']) ? $loan['reasonforloan'] ?? '' : '';
                $result['requested_amount'] = !is_array($loan['loanamount']) ? $loan['loanamount'] ?? '' : '';
                $result['credit_score'] = !is_array($loan['creditscore']) ? $loan['creditscore'] ?? '' : '';
            } else {
                $result['requested_amount'] = !is_array($personal['requestedamount']) ? $personal['requestedamount'] ?? 0 : 0;
            }
            $this->merge($result);
        }
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
