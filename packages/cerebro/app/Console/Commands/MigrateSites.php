<?php

#### THIS MIGRATION IS ONLY FOR DEVELEPMENT DEPLOY !!!!!!!
#### DO NOT START IT IN PRODUCTION !!!!!!111

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sites;
use App\Services\ThemeSettings;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class MigrateSites extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sites:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $storage = null;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->confirm('Do you wish to proceed with the migration?')) {
            $this->info('Migration aborted.');
            return Command::FAILURE;
        }

        $this->info('Migrating started...');

        // $sites = Sites::where('id', '<', 52)->get();
        $sites = Sites::get();
        foreach ($sites as $site) {

            switch($site->theme) {
                case 'lendingsource':
                    $this->migrateLendingsourse($site);
                    break;
                case 'loan5000':
                    $this->migrateLoan5000($site);
                    break;
                case 'loan10000':
                    $this->migrateLoan10000($site);
                    break;
                case 'lendingnext':
                    $this->migrateLendingnext($site);
                    break;

            }
            $site->refreshCache();
        }

        $this->info('Migrating finished');

        return Command::SUCCESS;
    }

    private function copyFile($site, $file)
    {
        if (!$file) {
            return '';
        }

        $currentDomainName = $site->domain_name;
        $cloudFrontDomain = config('filesystems.cloudFrontUrl');
        $fromUrl = "https://d196n3hla5536u.cloudfront.net/common/app/sites/$file";
        $fileName = basename($fromUrl);
        $toUrl = 'https://' . $cloudFrontDomain . '/common/app/sites/' . $currentDomainName . '/' . $fileName;
        $info = pathinfo($fromUrl);
        $fileName = $info['basename'];

        // if (!Storage::disk('sitesResources')->exists($currentDomainName . '/' . $fileName)) {

        $response = Http::get($fromUrl);
        if ($response->successful()) {
            // var_dump($response->body());
            Storage::disk('sitesResources')->put($currentDomainName . '/' . $fileName, $response->body());
            $this->info($toUrl);
        }
        // }

        return $currentDomainName . '/' . $fileName;

    }

    private function migrateLendingsourse($site)
    {

        $theme = (new ThemeSettings)->getSettings($site->theme);

        $theme['general']['favicon'] = $site->favicon;
        //$theme['general']['favicon'] = $this->copyFile($site, $site->favicon);
        $theme['general']['main_color']['value'] = $site->main_color;
        $theme['general']['main_button_color']['value'] = $site->button_color;
        $theme['general']['link_color']['value'] = $site->link_color;
        $theme['general']['radio_button_color']['value'] = $site->radio_color;
        $theme['general']['radio_button_text_color']['value'] = $site->radio_text_color;

        $theme['elements']['EE_header']['CE_header_logo']['src'] = $site->logo;
        //$theme['elements']['EE_header']['CE_header_logo']['src'] = $this->copyFile($site, $site->logo);
        $theme['elements']['EE_footer']['CE_footer_logo']['src'] = $site->logo;
        //$theme['elements']['EE_footer']['CE_footer_logo']['src'] = $this->copyFile($site, $site->logo);

        $theme['elements']['EE_footer']['CE_footer_link']['color'] = $site->main_color;

        $theme['elements']['EE_main_button']['background-color'] = $site->main_color;
        $theme['elements']['EE_main_button'][':hover']['background-color']['value'] = $site->main_color;

        $theme['elements']['EE_big_button_1']['background-color'] = $site->button_color;
        $theme['elements']['EE_big_button_1'][':hover']['background-color']['value'] = $site->button_color;
        $theme['elements']['EE_big_button_1'][':hover']['color']['value'] = $site->link_color;

        $theme['elements']['EE_big_button_2']['background-color'] = $site->button_color;
        $theme['elements']['EE_big_button_2'][':hover']['background-color']['value'] = $site->button_color;
        $theme['elements']['EE_big_button_2'][':hover']['color']['value'] = $site->link_color;

        $theme['elements']['EE_big_button_3']['background-color'] = $site->button_color;
        $theme['elements']['EE_big_button_3'][':hover']['background-color']['value'] = $site->button_color;
        $theme['elements']['EE_big_button_3'][':hover']['color']['value'] = $site->link_color;

        $theme['elements']['EE_hero']['background-image'] = $site->hero;
        //$theme['elements']['EE_hero']['background-image'] = $this->copyFile($site, $site->hero);
        $theme['elements']['EE_hero']['CE_hero_span']['color'] = $site->main_color;

        $theme['elements']['EE_hiw']['CE_card_num']['background-color'] = $site->main_color;
        $theme['elements']['EE_wu']['CE_green_box']['background-image'] = '';

        $site->theme_settings = $theme;
        $site->theme_settings_origin = $theme;
        $user = User::find($site->form_id);
        $site->token = User::regenerateToken($user);
        $site->save();
    }

    private function migrateLoan5000($site)
    {
        $theme = (new ThemeSettings)->getSettings($site->theme);

        $theme['general']['favicon'] = $site->favicon;
        //$theme['general']['favicon'] = $this->copyFile($site, $site->favicon);
        $theme['general']['main_color']['value'] = $site->main_color;
        $theme['general']['button_color']['value'] = $site->button_color;
        $theme['general']['button_hover_bg_color']['value'] = $site->button_color;
        $theme['general']['footer_bg_color']['value'] = $site->footer_background_color;
        $theme['general']['footer_button_color']['value'] = $site->footer_button_color;
        $theme['general']['footer_text_color']['value'] = $site->footer_text_color;

        $theme['elements']['EE_hero']['background-image'] = $site->hero;
        //theme['elements']['EE_hero']['background-image'] = $this->copyFile($site, $site->hero);
        $theme['elements']['EE_hero']['CE_hero_h1']['color'] = $site->main_color;
        $theme['elements']['EE_hero']['CE_hero_h2']['color'] = $site->main_color;

        $theme['elements']['EE_wu']['background-image'] = $site->why_us_bg;
        //$theme['elements']['EE_wu']['background-image'] = $this->copyFile($site, $site->why_us_bg);
        $theme['elements']['EE_wu']['CE_sublabel']['background-color'] = $site->main_color;
        $theme['elements']['EE_wu']['CE_h2']['color'] = $site->main_color;

        $theme['elements']['EE_advantages']['CE_bg']['background-image'] = $site->advantages_bg;
        //$theme['elements']['EE_advantages']['CE_bg']['background-image'] = $this->copyFile($site, $site->advantages_bg);
        $theme['elements']['EE_advantages']['CE_h2']['color'] = $site->main_color;
        $theme['elements']['EE_advantages']['CE_sublabel']['background-color'] = $site->main_color;
        $theme['elements']['EE_advantages']['CE_card_h3']['color'] = $site->main_color;

        $theme['elements']['EE_comfort']['CE_bg']['background-image'] = $site->comfort_bg;
        $theme['elements']['EE_comfort']['CE_h2']['color'] =  $site->main_color;
        $theme['elements']['EE_comfort']['CE_bg_color']['background-color'] =  $site->footer_background_color; 

        $theme['elements']['EE_last']['CE_bg']['background-image'] = $site->last_bg;
        $theme['elements']['EE_last']['CE_h2']['color'] = $site->main_color;

        $theme['elements']['EE_footer']['CE_footer_logo']['src'] = $site->footer_logo;
        //$theme['elements']['EE_footer']['CE_footer_logo']['src'] = $this->copyFile($site, $site->footer_logo);
        $theme['elements']['EE_footer']['CE_footer_link']['background-color'] = $site->footer_button_color;
        $theme['elements']['EE_footer']['CE_footer_link']['color'] = $site->main_color;
        $theme['elements']['EE_footer']['CE_footer_text']['color'] = $site->main_color;
        $theme['elements']['EE_footer']['background-color'] = $site->footer_background_color;

        $theme['elements']['EE_header']['CE_header_logo']['src'] = $site->logo;
        //$theme['elements']['EE_header']['CE_header_logo']['src'] = $this->copyFile($site, $site->logo);
        $theme['elements']['EE_header']['CE_header_logo2']['src'] = $site->logo;
        //$theme['elements']['EE_header']['CE_header_logo2']['src'] = $this->copyFile($site, $site->logo);

        $theme['elements']['EE_hiw']['CE_h2']['color'] = $site->main_color;
        $theme['elements']['EE_hiw']['CE_sublabel']['background-color'] = $site->main_color;
        $theme['elements']['EE_hiw']['CE_card_num']['background-color'] = $site->main_color;
        $theme['elements']['EE_hiw']['CE_card_arrow']['color'] = $site->main_color;
        $theme['elements']['EE_hiw']['CE_card_h3']['color'] = $site->main_color;

        $theme['elements']['EE_faq']['CE_h2']['color'] = $site->main_color;
        $theme['elements']['EE_faq']['CE_sublabel']['background-color'] = $site->main_color;
        $theme['elements']['EE_faq']['CE_icon']['background-color'] = $site->main_color;
        $theme['elements']['EE_faq']['CE_text']['color'] = $site->main_color;

        $site->theme_settings = $theme;
        $site->theme_settings_origin = $theme;
        $user = User::find($site->form_id);
        $site->token = User::regenerateToken($user);
        $site->save();
    }
    private function migrateLoan10000($site)
    {
        $theme = (new ThemeSettings)->getSettings($site->theme);

        $theme['general']['favicon'] = $site->favicon;
        //$theme['general']['favicon'] = $this->copyFile($site, $site->favicon);
        $theme['general']['main_color']['value'] = $site->main_color;
        $theme['general']['main_button_color']['value'] = $site->button_color;
        $theme['general']['button_color_second']['value'] = $site->button_color_second;
        $theme['general']['step_bar_color']['value'] = $site->step_bar_color;
        $theme['general']['link_color']['value'] = $site->link_color;
        $theme['general']['radio_button_color']['value'] = $site->radio_color;
        $theme['general']['radio_button_text_color']['value'] = $site->radio_text_color;

        $theme['elements']['EE_header']['CE_header_logo']['src'] = $site->logo;
        //$theme['elements']['EE_header']['CE_header_logo']['src'] = $this->copyFile($site, $site->logo);
        $theme['elements']['EE_footer']['CE_footer_logo']['src'] = $site->footer_logo;
        //$theme['elements']['EE_footer']['CE_footer_logo']['src'] = $this->copyFile($site, $site->footer_logo);

        $theme['elements']['EE_hero']['background-image'] = $site->hero;
        //$theme['elements']['EE_hero']['background-image'] = $this->copyFile($site, $site->hero);
        $theme['elements']['EE_advantages']['CE_bg']['background-image'] = $site->advantages_bg;
        //$theme['elements']['EE_advantages']['CE_bg']['background-image'] = $this->copyFile($site, $site->advantages_bg);
        $theme['elements']['EE_last']['CE_bg']['background-image'] = $site->last_bg;
        //$theme['elements']['EE_last']['CE_bg']['background-image'] = $this->copyFile($site, $site->last_bg);
        $theme['elements']['EE_hiw']['CE_pic1']['src'] = $site->comfort_bg;
        //$theme['elements']['EE_hiw']['CE_pic1']['src'] = $this->copyFile($site, $site->comfort_bg);
        $theme['elements']['EE_hiw']['CE_pic2']['src'] = $site->comfort_bg2;
        //$theme['elements']['EE_hiw']['CE_pic2']['src'] = $this->copyFile($site, $site->comfort_bg2);
        $theme['elements']['EE_hiw']['CE_h3']['color'] = $site->button_color;
        $theme['elements']['EE_hiw']['CE_svg']['fill'] = $site->button_color;

        $theme['elements']['EE_wu']['CE_bg']['background-image'] = $site->why_us_bg;
        //$theme['elements']['EE_wu']['CE_bg']['background-image'] = $this->copyFile($site, $site->why_us_bg);

        $site->theme_settings = $theme;
        $site->theme_settings_origin = $theme;
        $user = User::find($site->form_id);
        $site->token = User::regenerateToken($user);
        $site->save();
    }

    private function migrateLendingnext($site)
    {
        $theme = (new ThemeSettings)->getSettings($site->theme);

        $theme['general']['favicon'] = $site->favicon;
        //$theme['general']['favicon'] = $this->copyFile($site, $site->favicon);
        $theme['general']['main_color']['value'] = $site->main_color;
        $theme['general']['main_button_color']['value'] = $site->button_color;
        $theme['general']['second_button_color']['value'] = $site->button_color_second;
        $theme['general']['link_color']['value'] = $site->link_color;
        $theme['general']['radio_button_bg_color']['value'] = $site->radio_color;
        $theme['general']['radio_button_color']['value'] = $site->radio_text_color;
        $theme['general']['button_hover_bg_color']['value'] = $site->main_color;

        $theme['elements']['EE_header']['CE_header_logo']['src'] = $site->logo;
        //$theme['elements']['EE_header']['CE_header_logo']['src'] = $this->copyFile($site, $site->logo);
        $theme['elements']['EE_footer']['CE_footer_logo']['src'] = $site->logo;
        //$theme['elements']['EE_footer']['CE_footer_logo']['src'] = $this->copyFile($site, $site->logo);

        $theme['elements']['EE_hero']['background-image'] = $site->hero;
        //$theme['elements']['EE_hero']['background-image'] = $this->copyFile($site, $site->hero);
        $theme['elements']['EE_hero']['CE_hero']['background-image'] = $site->hero2;
        //$theme['elements']['EE_hero']['CE_hero']['src'] = $this->copyFile($site, $site->hero2);

        $theme['elements']['EE_wu']['background-image'] = $site->why_us_bg;
        //$theme['elements']['EE_wu']['background-image'] = $this->copyFile($site, $site->why_us_bg);
        $theme['elements']['EE_wu']['CE_easy']['src'] = $site->why_us_icon_easy;
        //$theme['elements']['EE_wu']['CE_easy']['src'] = $this->copyFile($site, $site->why_us_icon_easy);
        $theme['elements']['EE_wu']['CE_fast']['src'] = $site->why_us_icon_fast;
        //$theme['elements']['EE_wu']['CE_fast']['src'] = $this->copyFile($site, $site->why_us_icon_fast);
        $theme['elements']['EE_wu']['CE_secure']['src'] = $site->why_us_icon_secure;
        //$theme['elements']['EE_wu']['CE_secure']['src'] = $this->copyFile($site, $site->why_us_icon_secure);
        $theme['elements']['EE_wu']['CE_safe']['src'] = $site->why_us_bg2;
        //$theme['elements']['EE_wu']['CE_safe']['src'] = $this->copyFile($site, $site->why_us_bg2);

        $theme['elements']['EE_hiw']['CE_pic1']['src'] = $site->comfort_bg;
        //$theme['elements']['EE_hiw']['CE_pic1']['src'] = $this->copyFile($site, $site->comfort_bg);
        $theme['elements']['EE_hiw']['CE_pic2']['src'] = $site->comfort_bg2;
        //$theme['elements']['EE_hiw']['CE_pic2']['src'] = $this->copyFile($site, $site->comfort_bg2);
        $theme['elements']['EE_hiw']['CE_pic3']['src'] = $site->comfort_bg3;
        //$theme['elements']['EE_hiw']['CE_pic3']['src'] = $this->copyFile($site, $site->comfort_bg3);
        $theme['elements']['EE_hiw']['CE_pic4']['src'] = $site->comfort_bg4;
        //$theme['elements']['EE_hiw']['CE_pic4']['src'] = $this->copyFile($site, $site->comfort_bg4);


        $site->theme_settings = $theme;
        $site->theme_settings_origin = $theme;
        $user = User::find($site->form_id);
        $site->token = User::regenerateToken($user);
        $site->save();
    }
}
