<?php

namespace App\Models;

use App\Services\DataTablesService;
use Aws\Acm\AcmClient;
use Aws\CloudFront\CloudFrontClient;
use Aws\ElasticLoadBalancingV2\ElasticLoadBalancingV2Client;
use Aws\Acm\Exception\AcmException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class Sites extends Model
{
    use HasFactory;

    const CERT_PENDING = 'PENDING_VALIDATION';
    const CERT_ISSUED = 'ISSUED';
    const CERT_FAILED = 'FAILED';

    const LOAN_FORMS = [
        [
            'id' => 'lendingsource',
            'name' => 'Lending Source'
        ],
        [
            'id' => 'loan5000',
            'name' => 'Loan 5,000'
        ],
        [
            'id' => 'loan10000',
            'name' => 'Loan 10,000'
        ],
        [
            'id' => 'lendingnext',
            'name' => 'Lending Next',
            'disabled' => true
        ],
    ];
    const CREDIT_CARD_FORMS = [
        [
            'id' => 'creditCard',
            'name' => 'Credit Card'
        ]
    ];

    const CERT_STATUS_LABELS = [
        self::CERT_PENDING => 'Pending',
        self::CERT_ISSUED => 'Issued',
        self::CERT_FAILED => 'Failed',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'domain_name',
        'site_type',
        'form_id',
        'server_id',
        'title',
        'logo',
        'favicon',
        'hero',
        'theme',
        'main_color',
        'button_color',
        'button_color_second',
        'step_bar_color',
        'link_color',
        'radio_color',
        'radio_text_color',
        'footer_background_color',
        'footer_button_color',
        'footer_text_color',
        'comfort_button_color',
        'token',
        'cert_arn',
        'cert_rule_arn',
        'cert_listener_arn',
        'cert_status',

        'header',
        'sub_header',
        'sub_header_color',
        'sub_header_color_text',
        'card_button_text',
        'card_button_color_first',
        'card_button_color_second',

        'footer_logo',
        'why_us_bg',
        'advantages_bg',
        'comfort_bg',
        'comfort_bg2',
        'last_bg',

        'hero2',
        'header_color',
        'header_bg_color',
        'comfort_bg3',
        'comfort_bg4',
        'why_us_bg2',
        'why_us_icon_fast',
        'why_us_icon_easy',
        'why_us_icon_secure',
        'why_us_bg_color',
        'why_us_text_color',
        'why_us_cards_text_color',
        'why_us_cards_bg_color',

        'theme_settings',
        'theme_settings_origin',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    protected $casts = [
    ];

    protected $dates = [
    ];

    protected $attributes = [
        'flow_id' => 1,
    ];

    public function server()
    {
        return $this->belongsTo(Servers::class, 'server_id', 'id');
    }

    public function cardItems()
    {
        return $this->hasMany('App\Models\CardSiteItems', 'site_id', 'id');
    }

    public static function drawIndexTable(Request $request, DataTablesService $dataTablesService)
    {
        $columns = [
            ['db' => 'id', 'where' => 'id', 'dt' => 0],
            ['db' => 'domain_name', 'where' => 'si.domain_name', 'dt' => 1],
            ['db' => 'form_name', 'where' => 'u.name', 'dt' => 2],
            ['db' => 'server_name', 'where' => 'se.name', 'dt' => 3],
            ['db' => 'theme', 'where' => 'si.theme', 'dt' => 4],
            ['db' => 'is_ssl', 'where' => 'si.is_ssl', 'dt' => 5],
            ['db' => 'cert_status', 'where' => 'si.cert_status', 'dt' => 6, 'formatter' => function ($d) {
                if (empty($d)) {
                    return '-';
                }
                $style = $d == Sites::CERT_PENDING ? 'bg-warning text-dark' : ($d == Sites::CERT_FAILED ? 'bg-danger' : 'bg-success');
                $text = Sites::CERT_STATUS_LABELS[$d];
                return '<span class="text-white badge ' . $style . '">' . $text . '</span>';
            }],
            ['db' => 'cert_arn', 'where' => 'si.cert_arn', 'dt' => 7],
            ['db' => 'actions', 'dt' => 8, 'formatter' => function () {
                return;
            }],
        ];

        $where = $dataTablesService::filter($request, $columns);
        $order = $dataTablesService::columnOrder($columns);
        $start = $request->get('start');
        $length = $request->get('length');

        $database = config('database.connections.mysql.database');

        $sites = DB::select(DB::raw("SELECT SQL_CALC_FOUND_ROWS
            si.id,
            si.domain_name,
            si.is_ssl,
            si.theme,
            si.cert_arn,
            si.cert_status,
            u.name AS form_name,
            se.name AS server_name
            FROM $database.sites AS si
                LEFT JOIN $database.servers AS se ON se.id = si.server_id
                LEFT JOIN $database.users AS u ON u.id = si.form_id
            $where AND si.deleted_at IS NULL
            $order
            LIMIT $start, $length
        "));

        $filtered = DB::selectOne(DB::raw("SELECT FOUND_ROWS() AS quantity"));
        $total = DB::selectOne(DB::raw("SELECT COUNT(id) AS quantity FROM $database.sites WHERE deleted_at IS NULL "));

        $array = json_decode(json_encode($sites), true);

        return [
            "draw" => $request->get('draw') !== null ? $request->get('draw') : 0,
            "recordsTotal" => intval($total->quantity),
            "recordsFiltered" => intval($filtered->quantity),
            "data" => $dataTablesService::prepareData($array, $columns)
        ];
    }

    public function deleteSiteFiles()
    {
        if (!empty($this->logo) && Storage::disk('sitesResources')->exists($this->logo)) {
            Storage::disk('sitesResources')->delete($this->logo);
        }

        if (!empty($this->hero) && Storage::disk('sitesResources')->exists($this->hero)) {
            Storage::disk('sitesResources')->delete($this->hero);
        }

        if (!empty($this->favicon) && Storage::disk('sitesResources')->exists($this->favicon)) {
            Storage::disk('sitesResources')->delete($this->favicon);
        }

        if ($this->theme == 'loan5000' || $this->theme == 'loan10000') {
            if (!empty($this->why_us_bg) && Storage::disk('sitesResources')->exists($this->why_us_bg)) {
                Storage::disk('sitesResources')->delete($this->why_us_bg);
            }

            if (!empty($this->footer_logo) && Storage::disk('sitesResources')->exists($this->footer_logo)) {
                Storage::disk('sitesResources')->delete($this->footer_logo);
            }

            if (!empty($this->advantages_bg) && Storage::disk('sitesResources')->exists($this->advantages_bg)) {
                Storage::disk('sitesResources')->delete($this->advantages_bg);
            }

            if (!empty($this->comfort_bg) && Storage::disk('sitesResources')->exists($this->comfort_bg)) {
                Storage::disk('sitesResources')->delete($this->comfort_bg);
            }

            if (!empty($this->last_bg) && Storage::disk('sitesResources')->exists($this->last_bg)) {
                Storage::disk('sitesResources')->delete($this->last_bg);
            }
        }

        if ($this->theme == 'loan10000') {
            if (!empty($this->comfort_bg2) && Storage::disk('sitesResources')->exists($this->comfort_bg2)) {
                Storage::disk('sitesResources')->delete($this->comfort_bg2);
            }
        }
    }

    public function deleteCertificate()
    {
        $acm = new AcmClient([
            'region' => config('aws.region'),
            'version' => 'latest'
        ]);

        $elbClient = new ElasticLoadBalancingV2Client([
            'region' => config('aws.region'),
            'version' => 'latest'
        ]);
        $removeCert = false;
        if (!empty($this->cert_arn) && !empty($this->cert_listener_arn)) {
            $elbClient->removeListenerCertificates([
                'Certificates' => [
                    [
                        'CertificateArn' => $this->cert_arn,
                    ],
                ],
                'ListenerArn' => $this->cert_listener_arn
            ]);
            $removeCert = true;
        }

        if (!empty($this->cert_rule_arn)) {
            $elbClient->deleteRule([
                'RuleArn' => $this->cert_rule_arn
            ]);
        }
        if ($removeCert) {
            $attempts = true;
            while ($attempts) { // waiting while the certificate will be detached from the listener.
                try {
                    sleep(5);
                    $acm->deleteCertificate([
                        'CertificateArn' => $this->cert_arn
                    ]);
                    $attempts = false;
                } catch (AcmException $exception) {
                    continue;
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getCnameData()
    {
        $acm = new AcmClient([
            'region' => config('aws.region'),
            'version' => 'latest'
        ]);

        $pendingCert = $acm->describeCertificate([
            'CertificateArn' => $this->cert_arn
        ]);

        try {

            $domainValidationOptions = $pendingCert->search('Certificate.DomainValidationOptions');
            $this->cert_status = $pendingCert->search('Certificate.Status');
            $this->save();
            $result = [];
            foreach ($domainValidationOptions as $item) {
                $cnameKey = explode('.', $item['ResourceRecord']['Name']);

                $name = '';
                $count = (count($cnameKey)) - 3;
                for ($i = 0; $i < $count; $i++) {
                    $name .= $cnameKey[$i] . '.';
                }
                $name = substr($name, 0, -1);

                $result[] = [
                    'name' => $name,
                    'value' => $item['ResourceRecord']['Value']
                ];
            }

            return [
                'status' => true,
                'result' => $result
            ];

        } catch (\Exception $exception) {
            return [
                'status' => false,
                'message' => 'CNAME data is not ready yet, please try again later.',
            ];
        }
    }

    /**
     * @return array
     */
    public function requestCertificate()
    {
        $acm = new AcmClient([
            'region' => config('aws.region'),
            'version' => 'latest'
        ]);

        $cert = $acm->requestCertificate([
            'DomainName' => $this->domain_name,
            'SubjectAlternativeNames' => ['www.' . $this->domain_name],
            'ValidationMethod' => 'DNS',
            'KeyAlgorithm' => 'RSA_2048',
            'Options' => [
                'CertificateTransparencyLoggingPreference' => 'ENABLED',
            ],
        ]);

        try {
            $certArn = $cert->get('CertificateArn');
            $this->cert_arn = $certArn;
            $this->cert_status = self::CERT_PENDING;
            $this->save();
            return [
                'status' => true,
            ];
        } catch (\Exception $exception) {
            return [
                'status' => false,
                'message' => 'Something went wrong',
            ];
        }
    }

    public function addAlbCertificateListener()
    {
        $elbClient = new ElasticLoadBalancingV2Client([
            'region' => config('aws.region'),
            'version' => 'latest'
        ]);

        $loadBalancerArn = ($elbClient->describeLoadBalancers([
            'Names' => [config('aws.albName')
            ]]))->search('LoadBalancers[0].LoadBalancerArn');

        $listeners = $elbClient->describeListeners([
            'LoadBalancerArn' => $loadBalancerArn
        ]);

        $listenerArn = '';
        if (!empty($listeners)) {
            foreach ($listeners['Listeners'] as $listener) {
                if ($listener['Port'] == 443) {
                    $listenerArn = $listener['ListenerArn'];
                }
            }
        }

        $targetGroups = $elbClient->describeTargetGroups();

        $targetGroupArn = '';
        foreach ($targetGroups['TargetGroups'] as $targetGroup) {
            if (str_contains($targetGroup['TargetGroupName'], config('aws.frontRuleTargetGroup'))) {
                $targetGroupArn = $targetGroup['TargetGroupArn'];
            }
        }

        $rules = $elbClient->describeRules([
            'ListenerArn' => $listenerArn,
        ]);

        $priorities = [];
        $ruleExists = false;
        $existedRuleArn = null;
        foreach ($rules['Rules'] as $rule) {
            foreach ($rule['Conditions'] as $item) {
                if ($item['Field'] == 'host-header' && $item['Values'][0] == $this->domain_name) {
                    $ruleExists = true;
                    $existedRuleArn = $rule['RuleArn'];
                }
            }
            if ((int)$rule['Priority'] != 0) {
                $priorities[] = (int)$rule['Priority'];
            }
        }

        $priority = max($priorities) + 1;

        $elbClient->addListenerCertificates([
            'Certificates' => [
                [
                    'CertificateArn' => $this->cert_arn,
                ],
            ],
            'ListenerArn' => $listenerArn,
        ]);

        if (!$ruleExists) {
            $rule = $elbClient->createRule([
                'Actions' => [
                    [
                        'TargetGroupArn' => $targetGroupArn,
                        'Type' => 'forward',
                    ],
                ],
                'Conditions' => [
                    [
                        'Field' => 'path-pattern',
                        'Values' => [
                            '/*',
                        ],
                    ],
                    [
                        'Field' => 'host-header',
                        'Values' => [
                            $this->domain_name,
                            'www.' . $this->domain_name,
                        ],
                    ],
                ],
                'ListenerArn' => $listenerArn,
                'Priority' => $priority,
            ]);
        }

        $this->cert_rule_arn = $ruleExists ? $existedRuleArn : $rule->search('Rules[0].RuleArn');
        $this->cert_listener_arn = $listenerArn;
        $this->is_ssl = true;
        $this->save();
    }

    public function uploadImages($request)
    {
        if ($request->file('logo')) {
            $this->saveSiteImage($request, 'logo');
        }

        if ($request->file('hero')) {
            $this->saveSiteImage($request, 'hero');
        }

        if ($request->file('hero2')) {
            $this->saveSiteImage($request, 'hero2');
        }

        if ($request->file('favicon')) {
            $this->saveSiteImage($request, 'favicon');
        }

        if ($request->file('footer_logo')) {
            $this->saveSiteImage($request, 'footer_logo');
        }

        if ($request->file('why_us_bg')) {
            $this->saveSiteImage($request, 'why_us_bg');
        }

        if ($request->file('why_us_bg2')) {
            $this->saveSiteImage($request, 'why_us_bg2');
        }

        if ($request->file('why_us_icon_fast')) {
            $this->saveSiteImage($request, 'why_us_icon_fast');
        }

        if ($request->file('why_us_icon_easy')) {
            $this->saveSiteImage($request, 'why_us_icon_easy');
        }

        if ($request->file('why_us_icon_secure')) {
            $this->saveSiteImage($request, 'why_us_icon_secure');
        }

        if ($request->file('advantages_bg')) {
            $this->saveSiteImage($request, 'advantages_bg');
        }

        if ($request->file('comfort_bg')) {
            $this->saveSiteImage($request, 'comfort_bg');
        }

        if ($request->file('comfort_bg2')) {
            $this->saveSiteImage($request, 'comfort_bg2');
        }

        if ($request->file('comfort_bg3')) {
            $this->saveSiteImage($request, 'comfort_bg3');
        }

        if ($request->file('comfort_bg4')) {
            $this->saveSiteImage($request, 'comfort_bg4');
        }

        if ($request->file('last_bg')) {
            $this->saveSiteImage($request, 'last_bg');
        }
    }

    public function saveSiteImage($request, string $imageName)
    {
        if ($request->file($imageName)) {
            $ext = '.' . $request->file($imageName)->extension();

            if (!empty($this->$imageName) && Storage::disk('sitesResources')->exists($this->$imageName)) {
                Storage::disk('sitesResources')->delete($this->$imageName);
                $this->invalidateCloudFront($this->$imageName);
            }

            $this->$imageName = $request->file($imageName)->storeAs($this->domain_name, $imageName . $ext, ['disk' => 'sitesResources']);
        }
    }

    public function refreshCache()
    {
        Redis::set($this->domain_name, json_encode(Sites::find($this->id)));
    }

    /**
     * @param string $path
     * @return void|bool
     */
    public function invalidateCloudFront(string $path)
    {
        if (!config('dnm.cloudFrontDistributionInvalidate')) {
            return true;
        }

        $foundedDistribution = $this->getDistdibutionDetails();

        if (isset($foundedDistribution)) {

            $cloudFrontClient = new CloudFrontClient([
                'region' => config('aws.region'),
                'version' => 'latest'
            ]);

            $cloudFrontClient->createInvalidation([
                'DistributionId' => $foundedDistribution['id'], // REQUIRED
                'InvalidationBatch' => [
                    'CallerReference' => config('app.name') . '-' .time() . substr(sha1($path), 0, 10), // REQUIRED
                    'Paths' => [ // REQUIRED
                        'Items' => ['/common/app/sites/' . $path],
                        'Quantity' => '1', // REQUIRED
                    ],
                ],
            ]);
        }
    }

    public function getDistdibutionDetails()
    {
        $cloudFrontClient = new CloudFrontClient([
            'region' => config('aws.region'),
            'version' => 'latest'
        ]);

        $distributions = $cloudFrontClient->listDistributions();

        if (!empty($distributions)) {
            foreach ($distributions->search('DistributionList.Items') as $distribution) {
                if($distribution['Comment'] == config('dnm.cloudFrontDistributionDescription')) {
                    return ['id' => $distribution['Id'], 'domain' => $distribution['DomainName']];
                }
            }
        }
        return null;
    }

    /**
     * @param $url
     * @return string
     */
    static function getResourceUrl($url)
    {
        return !empty($url) ? Storage::disk('sitesResources')->url($url) . '?time=' . time() : null;
    }

    /**
     * Interact with the domain name.
     */
    protected function domainName(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strtolower($value),
        );
    }
}

