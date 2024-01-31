<?php

namespace App\Http\Controllers;

use App\Http\Requests\SitesRequest;
use App\Http\Requests\CustomSiteRequest;
use App\Models\CardSiteItems;
use App\Models\Servers;
use App\Models\Sites;
use App\Models\User;
use App\Services\DataTablesService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use App\Services\ThemeSettings;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class SitesController extends Controller
{
    public function sites()
    {
        return Inertia::render('Sites');
        // return view('dashboard.sites.sites');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, DataTablesService $dataTablesService)
    {
        return response()->json(Sites::drawIndexTable($request, $dataTablesService));
    }

    public function create(Request $request)
    {
        $type = $request->query('type');

        $servers = Servers::all('id', 'name');
        $forms = User::select('id', 'name')->whereNotIn('id', function ($query) {
            $query->select('form_id')->from('sites');
        })->get();
        $site = new Sites();

        if ($type === 'loan') {
            return Inertia::render('SiteEditor', [
                'servers' => $servers,
                'forms' => $forms,
                'loanFormThemes' => Sites::LOAN_FORMS,
                'creditCardThemes' => Sites::CREDIT_CARD_FORMS,
                'site' => $site,
                'siteItems' => collect()->add(new CardSiteItems(['benefits' => ['']])),
                'mode' => 'Create'
            ]);
        }
        

        return view('dashboard.sites.create', [
            'servers' => $servers,
            'forms' => $forms,
            'loanFormThemes' => Sites::LOAN_FORMS,
            'creditCardThemes' => Sites::CREDIT_CARD_FORMS,
            'site' => $site,
            'siteItems' => collect()->add(new CardSiteItems(['benefits' => ['']])),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    // public function store(SitesRequest $request)
    // {
    //     $site = new Sites($request->all());

    //     $site->uploadImages($request);

    //     $user = User::find($site->form_id);
    //     $site->token = User::regenerateToken($user);
    //     $site->save();
    //     $site->refreshCache();

    //     return response()->json(
    //         [
    //             'status' => 200
    //         ]
    //     );
    // }

    /**
     * Display the specified resource.
     *
     * @param integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    // public function show($id)
    // {
    //     $site = Sites::find($id);

    //     $site->logo = Sites::getResourceUrl($site->logo);
    //     $site->favicon = Sites::getResourceUrl($site->favicon);
    //     $site->hero = Sites::getResourceUrl($site->hero);
    //     $site->hero2 = Sites::getResourceUrl($site->hero2);

    //     $site->footer_logo = Sites::getResourceUrl($site->footer_logo);
    //     $site->why_us_bg = Sites::getResourceUrl($site->why_us_bg);
    //     $site->why_us_bg2 = Sites::getResourceUrl($site->why_us_bg2);
    //     $site->why_us_bg_color = Sites::getResourceUrl($site->why_us_bg_color);
    //     $site->why_us_icon_fast = Sites::getResourceUrl($site->why_us_icon_fast);
    //     $site->why_us_icon_easy = Sites::getResourceUrl($site->why_us_icon_easy);
    //     $site->why_us_icon_secure = Sites::getResourceUrl($site->why_us_icon_secure);
    //     $site->advantages_bg = Sites::getResourceUrl($site->advantages_bg);
    //     $site->comfort_bg = Sites::getResourceUrl($site->comfort_bg);
    //     $site->comfort_bg2 = Sites::getResourceUrl($site->comfort_bg2);
    //     $site->comfort_bg3 = Sites::getResourceUrl($site->comfort_bg3);
    //     $site->comfort_bg4 = Sites::getResourceUrl($site->comfort_bg4);
    //     $site->last_bg = Sites::getResourceUrl($site->last_bg);

    //     return response()->json(
    //         ['status' => 200, 'data' => $site]
    //     );
    // }

    /**
     * Display the specified resource.
     *
     * @param integer $id
     * @return \Illuminate\Contracts\View\View|\Inertia\Response
     */
    public function edit($id)
    {
        $servers = Servers::all('id', 'name');
        $forms = User::all();

        $site = Sites::find($id);

        if ($site->site_type === 'loan') {
            return Inertia::render('SiteEditor', [
                'servers' => $servers,
                'forms' => $forms,
                'loanFormThemes' => Sites::LOAN_FORMS,
                'creditCardThemes' => Sites::CREDIT_CARD_FORMS,
                'site' => $site,
                'siteItems' => $site->cardItems,
                'mode' => 'Edit'
            ]);
        }
        

        $site->logo = Sites::getResourceUrl($site->logo);
        $site->favicon = Sites::getResourceUrl($site->favicon);
        $site->hero = Sites::getResourceUrl($site->hero);
        $site->hero2 = Sites::getResourceUrl($site->hero2);

        $site->footer_logo = Sites::getResourceUrl($site->footer_logo);
        $site->why_us_bg = Sites::getResourceUrl($site->why_us_bg);
        $site->why_us_bg2 = Sites::getResourceUrl($site->why_us_bg2);
        $site->why_us_icon_fast = Sites::getResourceUrl($site->why_us_icon_fast);
        $site->why_us_icon_easy = Sites::getResourceUrl($site->why_us_icon_easy);
        $site->why_us_icon_secure = Sites::getResourceUrl($site->why_us_icon_secure);
        $site->advantages_bg = Sites::getResourceUrl($site->advantages_bg);
        $site->comfort_bg = Sites::getResourceUrl($site->comfort_bg);
        $site->comfort_bg2 = Sites::getResourceUrl($site->comfort_bg2);
        $site->comfort_bg3 = Sites::getResourceUrl($site->comfort_bg3);
        $site->comfort_bg4 = Sites::getResourceUrl($site->comfort_bg4);
        $site->last_bg = Sites::getResourceUrl($site->last_bg);


        return view('dashboard.sites.edit', [
            'servers' => $servers,
            'forms' => $forms,
            'loanFormThemes' => Sites::LOAN_FORMS,
            'creditCardThemes' => Sites::CREDIT_CARD_FORMS,
            'site' => $site,
            'siteItems' => $site->cardItems,
        ]);


    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    // public function update(SitesRequest $request, int $id)
    // {
    //     $site = Sites::find($id);
    //     $site->uploadImages($request);

    //     $site->theme = $request->theme;
    //     $site->title = $request->title;
    //     $site->main_color = $request->main_color;
    //     $site->button_color = $request->button_color;
    //     $site->button_color_second = $request->button_color_second;
    //     $site->step_bar_color = $request->step_bar_color;
    //     $site->link_color = $request->link_color;
    //     $site->radio_color = $request->radio_color;
    //     $site->radio_text_color = $request->radio_text_color;
    //     $site->footer_background_color = $request->footer_background_color;
    //     $site->footer_button_color = $request->footer_button_color;
    //     $site->footer_text_color = $request->footer_text_color;
    //     $site->comfort_button_color = $request->comfort_button_color;
    //     $site->header_color = $request->header_color;
    //     $site->header_bg_color = $request->header_bg_color;
    //     $site->why_us_bg_color = $request->why_us_bg_color;
    //     $site->why_us_text_color = $request->why_us_text_color;
    //     $site->why_us_cards_text_color = $request->why_us_cards_text_color;
    //     $site->why_us_cards_bg_color = $request->why_us_cards_bg_color;

    //     $site->update();
    //     $site->refreshCache();


    //     return response()->json(
    //         ['status' => 200]
    //     );
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $site = Sites::find($id);

        if ($site) {
            if (in_array(config('app.env'), ['production', 'dev'])) {
                $site->deleteCertificate();
            }

            if ($site->site_type == 'card') {
                $cardItems = CardSiteItems::where(['site_id' => $site->id])->get();
                foreach ($cardItems as $cardItem) {
                    $cardItem->deleteItemImage();
                    $cardItem->delete();
                }
            }
            $site->deleteSiteFiles();
            $site->delete();
            return response()->json(
                ['status' => 200]
            );
        }
        return response()->json(
            ['status' => 500, 'message' => 'Site not found']
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestCert($id)
    {
        $site = Sites::find($id);

        if (!$site) {
            return response()->json(
                ['status' => 500, 'message' => 'Site not found']
            );
        }

        $result = $site->requestCertificate();

        if ($result['status']) {
            return response()->json(
                [
                    'status' => 200,
                    'id' => $id,
                ]
            );
        }

        return response()->json(
            ['status' => 500, 'message' => $result['message']]
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCname($id)
    {
        $site = Sites::find($id);

        if (!$site) {
            return response()->json(
                ['status' => 500, 'message' => 'Site not found']
            );
        }

        $result = $site->getCnameData();

        if ($result['status']) {
            return response()->json(
                [
                    'status' => 200,
                    'id' => $id,
                    'data' => $result['result']
                ]
            );
        }

        return response()->json(
            ['status' => 500, 'message' => $result['message']]
        );
    }

    //////////////////////////////////////////////////////////////////////////////////

    public function getThemeSettings(Request $request)
    {
        $themeName = $request->input('themeName');
        $siteId = $request->input('siteId') ?? null;

        if ($siteId) {
            $site = Sites::find($siteId);

            if ($site) {
                $settings = json_decode($site->theme_settings, true);
                if (!empty($settings)) {
                    $themeSettings = new ThemeSettings();
                    $themeSettings->processThemeSettings($settings);
                } else {
                    $themeSettings = new ThemeSettings();
                    $settings = $themeSettings->getSettings($site->theme);
                }
                $settings['htmlContent'] = $this->getHtmlContent($site->theme);

            } else {
                return response()->json(['error' => 'Site not found'], 404);
            }
        } else {
            $themeSettings = new ThemeSettings();
            $settings = $themeSettings->getSettings($themeName);
            $settings['htmlContent'] = $this->getHtmlContent($themeName);
        }

        if ($settings) {
            return response()->json($settings, 200, [], JSON_UNESCAPED_SLASHES);
        } else {
            return response()->json(['error' => 'Theme settings not found'], 404);
        }
    }


    public function mergeSiteSettings(Request $request)
    {
        // var_dump($request->all());
        $themeName = $request->input('themeName');
        $siteId = $request->input('siteId');

        $themeSettings = new ThemeSettings();
        $settings = $themeSettings->mergeSiteSettings($themeName, $siteId);
        $settings['htmlContent'] = $this->getHtmlContent($themeName);

        if ($settings) {
            return response()->json($settings, 200, [], JSON_UNESCAPED_SLASHES);
        } else {
            return response()->json(['error' => 'Theme settings not found'], 404);
        }
    }

    private function getHtmlContent($themeName)
    {
        $htmlFilePath = resource_path("themes/$themeName/index.html");
        if (File::exists($htmlFilePath)) {
            return File::get($htmlFilePath);
        } else {
            return 'HTML content not found.';
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CustomSiteRequest $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(CustomSiteRequest $request)
    {
        $siteId = $request->input('siteId');
        $site = $siteId ? Sites::find($siteId) : new Sites();

        if (!$site) {
            return response()->json(['error' => 'Site not found'], 404);
        }

        if (!$siteId) {
            $site->domain_name = $request->input('domainName');
            $site->form_id = $request->input('selectedForm');
            $site->server_id = $request->input('selectedServer');
        }

        $site->theme = $request->input('selectedTheme');
        $user = User::find($site->form_id);
        $site->token = User::regenerateToken($user);
        $site->title = $request->input('title');

        $themeSettings = json_decode($request->input('themeSettings'), true);
        $fileIdMapping = json_decode($request->input('fileIdMapping'), true);
        $newSavedFiles = [];
        
        $currentDomainName = $site->domain_name;

        array_walk_recursive($themeSettings, function (&$value, $key) use ($currentDomainName, $site, &$newSavedFiles) {
            if (in_array($key, ['favicon', 'background-image', 'src'])) {
        
                $value = preg_replace('/^url\(\s*["\']?|["\']?\s*\)$/', '', $value);
                
                if (strpos($value, 'http://') !== false || strpos($value, 'https://') !== false) {
                    $parsedUrl = parse_url($value);
                    $path = $parsedUrl['path'];
        
                    $pathParts = explode('/', $path);
                    if (count($pathParts) > 2) {
                        $domainPart = $pathParts[count($pathParts) - 2];
                        $filePart = $pathParts[count($pathParts) - 1];
                        $value = $domainPart . '/' . $filePart; 
                        // var_dump($value);
                    }
                    return;
                }
        
                $parsedUrl = parse_url($value);
                $path = $parsedUrl['path'] ?? '';
                $fileName = basename($path);
        
                if (!!$fileName) {
                    $localFilePath = public_path()  . '/' .  $site->theme . '/img/' . $fileName;
                    if (File::exists($localFilePath)) {
                        $storagePath = $currentDomainName . '/' . $fileName;
                        Storage::disk('sitesResources')->put($storagePath, File::get($localFilePath));
                        
                        $newFilePath = $currentDomainName . '/' . $fileName;
                        $newSavedFiles[] = $newFilePath;
                        $value = $newFilePath;
                    }
                }
            }
        });
        
        foreach ($fileIdMapping as $elementId => $properties) {
            if (is_array($properties)) {
                foreach ($properties as $property => $fileName) {
                    $this->handleFileName($fileName, $elementId, $property, $request, $currentDomainName, $themeSettings);
                }
            } else if (is_string($properties)) {
                $this->handleFileName($properties, $elementId, null, $request, $currentDomainName, $themeSettings);
            }
        }
        
        $site->theme_settings = json_encode($themeSettings, JSON_UNESCAPED_SLASHES);
        $site->save();
        $site->refreshCache();

        return response()->json([
            'success' => 'Site saved successfully',
            'siteId' => $site->id, 
        ], 200);
    }

    private function handleFileName($fileName, $elementId, $property, $request, $currentDomainName, &$themeSettings)
    {
        if (is_array($fileName)) {
            foreach ($fileName as $fileType => $fileValue) {
                $this->uploadFile($fileValue, $elementId, $property, $fileType, $request, $currentDomainName, $themeSettings);
            }
        } else {
            $this->uploadFile($fileName, $elementId, $property, null, $request, $currentDomainName, $themeSettings);
        }
    }

    private function uploadFile($fileValue, $elementId, $property, $fileType, $request, $currentDomainName, &$themeSettings)
    {
        $fileKey = str_replace(['.', ' '], '_', $fileValue);
        if ($request->hasFile($fileKey)) {
            $file = $request->file($fileKey);
            $newFileName = $currentDomainName . '/' . $fileKey . '-' . $file->getClientOriginalName();
            Storage::disk('sitesResources')->delete($newFileName);
            Storage::disk('sitesResources')->put($newFileName, file_get_contents($file));
            
            if ($fileType !== null) {
                $themeSettings['elements'][$elementId][$property][$fileType] = $newFileName;
            } else if ($property !== null) {
                $themeSettings['elements'][$elementId][$property] = $newFileName;
            } else {
                $themeSettings['general'][$elementId] = $newFileName;
            }
        }
    }

}
