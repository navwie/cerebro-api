<?php

namespace App\Http\Requests;

use App\Models\Customer;
use App\Models\User;
use App\Models\CustomerToBank;
use App\Models\Reapply;
use App\Rules\Aba;
use App\Rules\Email;
use App\Rules\ssnAuth;
use App\Rules\UsaPhone;
use App\Rules\UsaZip;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

class ApiMainUpdateRequest extends FormRequest
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
        if($this->input('reapply_id') != null || $this->input('reapply_id') != ''){
            $reapply = Reapply::find($this->input('reapply_id'));
        }else{
            return ['reapply_id' => ['required', 'numeric']];
        }
        $validations = [
            'reapply_id' => ['required', 'numeric'],
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
            'credit_score' => ['required', 'numeric', 'max:999'],
            'ssn_non_encrypted' => ['required' ,'numeric', 'digits:9',new ssnAuth($this->input('reapply_id'))],
            'dob' => ['required', 'date_format:m-d-Y', 'before:'.\Carbon\Carbon::now()->subYears(18)->format('Y-m-d'), 'after:'.\Carbon\Carbon::now()->subYears(120)->format('Y-m-d')],
            'first_name' => ['required', 'string', 'min:2', 'max:128'],
            'last_name' => ['required', 'string', 'min:2', 'max:128'],
            'address' => ['required', 'string', 'min:5', 'max:128'],
            'city' => ['required', 'string', 'min:2', 'max:128'],
            'state' => ['required', 'string', 'size:2'],
            'zip' => [
                'required',
                'size:5',
                new UsaZip()
            ],
            'address_month' => ['required', 'numeric', 'min:0', 'max:860'],
            'rent_or_own' => ['required', 'boolean'],
            'email' => ['required', 'email:rfc,dns','unique:customers,email,'.$reapply->customer->id, new Email()],
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
        if (Route::getCurrentRoute()->getActionMethod() == 'update_main') {
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
    }
}
