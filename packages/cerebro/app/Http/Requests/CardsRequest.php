<?php

namespace app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CardsRequest extends FormRequest
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
        $validate = [
            'site_type' => 'required|in:loan,card',
            'domain_name' => ['required_without:id', 'unique:sites', 'min:1', 'max:255', 'regex:(^([a-zA-Z0-9][a-zA-Z0-9-_]*\.)*[a-zA-Z0-9]*[a-zA-Z0-9-_]*[[a-zA-Z0-9]+$)'],
            'form_id' => 'required_without:id|unique:sites',
            'server_id' => 'required_without:id',
            'logo' => 'required_without:id|image',
            'favicon' => 'required_without:id|file|mimes:ico',
            'hero' => 'required_without:id|image',
            'theme' => 'required|max:255',
            'title' => 'required|max:255',
            'header' => 'required|max:255',
            'sub_header' => 'required|max:255',
            'sub_header_color_text' => 'required|max:255',
            'sub_header_color' => 'required|max:255',
            'card_button_color_first' => 'required|max:255',
            'card_button_color_second' => 'required|max:255',
            'card_button_text' => 'required|max:255',

            'card_item.*.name' => 'required|max:255',
            'card_item.*.image' => 'required_without:card_item.*.id|image',
            'card_item.*.stars' => 'required|numeric|min:1|max:5',
            'card_item.*.description' => 'required|string',
            'card_item.*.btn_color_first' => 'required|max:255',
            'card_item.*.btn_color_second' => 'required|max:255',
            'card_item.*.btn_text' => 'required|max:20',
            'card_item.*.btn_url' => ['required', 'max:255', 'regex:/^((?:https?\:\/\/|www\.)(?:[-a-z0-9]+\.)*[-a-z0-9]+.*)$/'],
            'card_item.*.benefits.*' => 'required|max:255',
        ];
        return $validate;
    }
}
