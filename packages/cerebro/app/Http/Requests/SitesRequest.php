<?php

namespace App\Http\Requests;

use App\Models\Sites;
use Illuminate\Foundation\Http\FormRequest;

class SitesRequest extends FormRequest
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
            'domain_name' => ['required_without:id', 'unique:sites', 'min:1', 'max:255', 'regex:(^([a-zA-Z0-9][a-zA-Z0-9-_]*\.)*[a-zA-Z0-9]*[a-zA-Z0-9-_]*[[a-zA-Z0-9]+$)'],
            'form_id' => 'required_without:id|unique:sites',
            'server_id' => 'required_without:id',
            'logo' => 'required_without:id|image',
            'favicon' => 'required_without:id|file|mimes:ico',
            'hero' => 'required_without:id|image',
            'theme' => 'required|max:255',
            'title' => 'required|max:255',
            'main_color' => 'required|max:255',
            'button_color' => 'required|max:255',
        ];

        switch (request('theme')) {
            case 'lendingsource':

                $validate = array_merge($validate, [
                    'radio_color' => 'required|max:255',
                    'radio_text_color' => 'required|max:255',
                    'link_color' => 'required|max:255'
                ]);
                break;
            case 'loan5000':

                $whyUsBg = ['why_us_bg' => 'required_without:id|image'];
                $advantagesBg = ['advantages_bg' => 'required_without:id|image'];
                $comfortBg = ['comfort_bg' => 'required_without:id|image'];
                $lastBg = ['last_bg' => 'required_without:id|image'];
                $footerLogo = ['footer_logo' => 'required_without:id|image'];
                $footerBackgroundColor = ['footer_background_color' => 'required|max:255'];
                $validate = array_merge($validate, [
                    'footer_button_color' => 'required|max:255',
                    'footer_text_color' => 'required|max:255',
                    'comfort_button_color' => 'required|max:255',
                ]);
                if (request('id')) {

                    $site = Sites::find(request('id'));

                    if (empty($site->why_us_bg)) {
                        $whyUsBg = ['why_us_bg' => 'required|image'];
                    }

                    if (empty($site->advantages_bg)) {
                        $advantagesBg = ['advantages_bg' => 'required|image'];
                    }

                    if (empty($site->comfort_bg)) {
                        $comfortBg = ['comfort_bg' => 'required|image'];
                    }

                    if (empty($site->last_bg)) {
                        $lastBg = ['last_bg' => 'required|image'];
                    }

                    if (empty($site->footer_logo)) {
                        $footerLogo = ['footer_logo' => 'required|image'];
                    }
                }

                $validate = array_merge($validate, $whyUsBg, $advantagesBg, $comfortBg, $lastBg, $footerLogo, $footerBackgroundColor);
                break;

            case 'loan10000':
                $whyUsBg = ['why_us_bg' => 'required_without:id|image'];
                $advantagesBg = ['advantages_bg' => 'required_without:id|image'];
                $comfortBg = ['comfort_bg' => 'required_without:id|image'];
                $comfortBg2 = ['comfort_bg2' => 'required_without:id|image'];
                $lastBg = ['last_bg' => 'required_without:id|image'];
                $footerLogo = ['footer_logo' => 'required_without:id|image'];
                $buttonColorSecond = ['button_color_second' => 'required|max:255'];
                $stepBarColor = ['step_bar_color' => 'required|max:255'];

                if (request('id')) {

                    $site = Sites::find(request('id'));

                    if (empty($site->why_us_bg)) {
                        $whyUsBg = ['why_us_bg' => 'required|image'];
                    }

                    if (empty($site->advantages_bg)) {
                        $advantagesBg = ['advantages_bg' => 'required|image'];
                    }

                    if (empty($site->comfort_bg)) {
                        $comfortBg = ['comfort_bg' => 'required|image'];
                    }

                    if (empty($site->comfort_bg2)) {
                        $comfortBg2 = ['comfort_bg2' => 'required|image'];
                    }

                    if (empty($site->last_bg)) {
                        $lastBg = ['last_bg' => 'required|image'];
                    }

                    if (empty($site->footer_logo)) {
                        $footerLogo = ['footer_logo' => 'required|image'];
                    }
                }

                $validate = array_merge($validate, $whyUsBg, $advantagesBg, $comfortBg, $comfortBg2, $lastBg, $footerLogo, $buttonColorSecond, $stepBarColor);
                break;

            case 'lendingnext':
                $heroBg2 = ['hero2' => 'required_without:id|image'];
                $whyUsBg = ['why_us_bg' => 'required_without:id|image'];
                $whyUsBg2 = ['why_us_bg2' => 'required_without:id|image'];
                $whyUsIconFast = ['why_us_icon_fast' => 'required_without:id|image'];
                $whyUsIconEasy = ['why_us_icon_easy' => 'required_without:id|image'];
                $whyUsIconSecure = ['why_us_icon_secure' => 'required_without:id|image'];
                $comfortBg = ['comfort_bg' => 'required_without:id|image'];
                $comfortBg2 = ['comfort_bg2' => 'required_without:id|image'];
                $comfortBg3 = ['comfort_bg3' => 'required_without:id|image'];
                $comfortBg4 = ['comfort_bg4' => 'required_without:id|image'];

                if (request('id')) {

                    $site = Sites::find(request('id'));

                    if (empty($site->hero2)) {
                        $heroBg2 = ['hero2' => 'required|image'];
                    }

                    if (empty($site->why_us_bg)) {
                        $whyUsBg = ['why_us_bg' => 'required|image'];
                    }

                    if (empty($site->why_us_bg2)) {
                        $whyUsBg2 = ['why_us_bg2' => 'required|image'];
                    }

                    if (empty($site->why_us_bg2)) {
                        $whyUsIconFast = ['why_us_icon_fast' => 'required|image'];
                    }

                    if (empty($site->why_us_bg2)) {
                        $whyUsIconEasy = ['why_us_icon_easy' => 'required|image'];
                    }

                    if (empty($site->why_us_bg2)) {
                        $whyUsIconSecure = ['why_us_icon_secure' => 'required|image'];
                    }

                    if (empty($site->comfort_bg)) {
                        $comfortBg = ['comfort_bg' => 'required|image'];
                    }

                    if (empty($site->comfort_bg2)) {
                        $comfortBg2 = ['comfort_bg2' => 'required|image'];
                    }

                    if (empty($site->comfort_bg3)) {
                        $comfortBg3 = ['comfort_bg3' => 'required|image'];
                    }

                    if (empty($site->comfort_bg4)) {
                        $comfortBg4 = ['comfort_bg4' => 'required|image'];
                    }
                }

                $validate = array_merge($validate, $heroBg2, $whyUsBg, $whyUsBg2, $comfortBg, $comfortBg2, $comfortBg3, $comfortBg4, $whyUsIconFast, $whyUsIconEasy, $whyUsIconSecure);
                break;
        }

        return $validate;
    }
}
