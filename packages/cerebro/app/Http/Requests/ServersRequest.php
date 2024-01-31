<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServersRequest extends FormRequest
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
            'name' => 'required|unique:servers,name,' . request('id') . '|min:1|max:255',
            'ip_address' => 'required|unique:servers,ip_address,'. request('id'),
        ];
    }
}
