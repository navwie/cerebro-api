<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Reapply;
use App\Models\Customer;
use App\Models\CustomerToBank;
use App\Rules\UsaZip;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

class ApiReapplyUpdateRequest extends FormRequest
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
        if(config('logging.channels.reapply_request.turn')) {
            Log::channel('reapply_request')->notice(json_encode($this->request->all()));
        }
        $validations = [
            'requested_amount' => ['required', 'numeric'],
            'net_month_income' => ['required', 'numeric'],
            'credit_score' => ['required', 'numeric', 'max:999'],
            'ssn' => ['required', 'numeric', 'digits:4'],
            'job_title' => ['required', 'string','max:128'],
            'next_pay_day' => ['required', 'date_format:m-d-Y', 'after:yesterday', 'before:'.\Carbon\Carbon::now()->addYear()->format('Y-m-d')],
            'second_pay_day' => ['required', 'date_format:m-d-Y', 'after:next_pay_day', 'before:'.\Carbon\Carbon::now()->addYear()->format('Y-m-d')],
            'pay_frequency' => [
                'required',
                'string',
                'in:' . implode(',', Reapply::TYPE_PAY_FREQUENCY)
            ],
            'email' => ['required', 'email:rfc,dns'],
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
        if (Route::getCurrentRoute()->getActionMethod() == 'update_reapply') {
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
            empty($this->account_number) ? '' : 'account_number' => openssl_encrypt($this->account_number,'aes-128-cbc',config('hashing.hashing_key'),0,config('hashing.hashing_iv')),
            'sub_ids' => json_encode($this->sub_ids, true, JSON_THROW_ON_ERROR),
        ]);
    }
}
