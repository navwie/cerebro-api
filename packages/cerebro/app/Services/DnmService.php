<?php

namespace App\Services;


use App\Models\Customer;
use App\Models\CustomerAudit;
use App\Models\DecisionAudit;
use App\Models\LogDnm;
use App\Models\ReapplyAudit;
use App\Models\LogPixel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DnmService
{

    private array $fakeResponses = [
        'test' => [
            "id" => "0",
            "status" => "test",
            "message" => "TEST DECISION",
            "price" => "1",
            "redirect" => "https://dot818.com/",
            "fake_response" => 1,
        ],
        'error' => [
            "id" => "0",
            "status" => "error",
            "message" => "TEST DECISION",
            "fake_response" => 1,
        ],
        'reject' => [
            "id" => "0",
            "status" => "reject",
            "message" => "TEST DECISION",
            "fake_response" => 1,
        ],
        'sold' => [
            "id" => "0",
            "status" => "sold",
            "message" => "TEST DECISION",
            "price" => "1",
            "redirect" => "https://dot818.com/",
            "fake_response" => 1,
        ]
    ];

    /**
     * @param $formData
     * @return mixed|void
     */
    public function sendRequest($dataSend, $url, $reapplyData, $dataFromUser, $logId)
    {
        if (config('dnm.test_mode') && strtolower($dataFromUser['first_name']) == "test") {
            return $this->fakeResponse($reapplyData, $dataFromUser);
        }
        try{
            $ch = curl_init();
            if (!$ch) {
                die("Couldn't initialize a cURL handle");
            }
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "$dataSend");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $logDNM = LogDnm::create([
                'log_id' => $logId,
                'sent_dnm' => $dataSend,
            ]);

            $response = curl_exec($ch);
            if(config('logging.channels.dnmstats.turn')){
                Log::channel('dnmstats')->notice($response);
            }

            $logDNM->update(['response_dnm' => $response]);

            curl_close($ch);
            $xml = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
            $json = json_encode($xml);
            unify_json($json);
            if(config('logging.channels.dnmstats.turn')){
                Log::channel('dnmstats')->notice(json_encode($dataSend));
                Log::channel('dnmstats')->notice($json);
            }
            return json_decode($json,TRUE);
        }catch (\Exception $e) {
            if(config('logging.channels.dnm_send_request_error.turn')){
                Log::channel('dnm_send_request_error')->notice('line ' . $e->getLine() . ' :' . $e->getMessage());
                Log::channel('dnm_send_request_error')->notice("response:$response");
            }
            LogDnm::create([
                'log_id' => $logId,
                'sent_dnm' => $dataSend,
                'response_dnm' => $response
            ]);
            return [
                'status' => 'reject',
                'message' => 'ERROR ON SEND REQUEST'
            ];
        }
    }

    public function fakeResponse($reapplyData, $formData){
        $statuses = explode('-',$reapplyData['customer']['last_name']);
        $sleep = $reapplyData['customer']['job_title'];
        if(strtolower($reapplyData['customer']['emp_name']) == "sleep" && is_numeric($sleep) &&  $sleep > 0){
            Log::channel('debugging')->notice(json_encode($reapplyData['customer']));
            Log::channel('debugging')->notice($sleep);
            sleep($sleep);
        }
        if(count($statuses) > 1){
            if($formData['auto_redirect_to_lead']){
                return $this->fakeResponses[$statuses[1]] ?? $this->fakeResponses['test'];
            }else{
                return $this->fakeResponses[$statuses[0]] ?? $this->fakeResponses['test'];
            }
        }else{
            return $this->fakeResponses[$reapplyData['customer']['last_name']] ?? $this->fakeResponses['test'];
        }
    }

    public static function getClickIdRequest(Request $request)
    {
        $params = $request->all();
        $params['offer'] = $params['offer'] ?? 171;

        $url = config('dnm.urls.get_click_id') . '?' . http_build_query($params);
        $ch = curl_init();
        if (!$ch) {
            die("Couldn't initialize a cURL handle");
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);//without this line curl will return 301
        $response = json_decode(curl_exec($ch),true);
        Log::channel('get_click_id_request')->notice("getClickIdRequest Url: $url | getClickIdRequest response: " . json_encode($response));
        curl_close($ch);
        return response()->json(['click_id' => $response['clickid'] ?? '']);
    }

    public static function postBackRequest($decisionAuditId)
    {
        $decisionAudit = DecisionAudit::find($decisionAuditId);
        $reapplyAudit = ReapplyAudit::find($decisionAudit->reapply_id);
        $customerAudit = CustomerAudit::find($reapplyAudit->customer_id);
        $customer = Customer::where('email',$customerAudit->email)->first();
        if($reapplyAudit->click_id !== 'null' && $reapplyAudit->click_id !== null){
            $user = auth()->user();
            $url = $user->post_back_url;
            if(!$url == '' || !$url == null){
                $transactionId = hash('sha256',$customerAudit->first_name . $customerAudit->last_name . $reapplyAudit->updated_at);
                $search = ['[clickId]','[amount]','[transactionId]'];
                $replace = [$reapplyAudit->click_id,$decisionAudit->decision_price,$transactionId];
                $reapplyAudit->transaction_id = $transactionId;
                $reapplyAudit->saveQuietly();
                $url = str_replace($search,$replace,$url);
                $ch = curl_init();
                if(config('logging.channels.dnmstats.turn')){
                    Log::channel('dnmstats')->notice("Url for post back request: $url");
                }
                try{
                    ReapplyAudit::where([
                        ['customer_id', $decisionAudit->customer_id]
                    ])->latest()->first()->update(['transaction_id' => $transactionId]);
                } catch (\Exception $e) {
                    if(config('logging.channels.decisions.turn')){
                        Log::channel('dnmstats')->notice('line ' . $e->getLine() . ' :' . $e->getMessage());
                        Log::channel('dnmstats')->notice('customer_id:' . $decisionAudit->customer_id);
                        Log::channel('dnmstats')->notice('decision_audit_id:' . $decisionAudit->id);
                    }
                }
                if (!$ch) {
                    die("Couldn't initialize a cURL handle");
                }
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                LogPixel::where([
                    'customer_id' => $customer->id,
                ])
                    ->latest()
                    ->first()
                    ->update([
                        'transaction_id' => $transactionId,
                        'sent_pixel' => $url,
                        'response_pixel' => $response
                    ]);
                if(config('logging.channels.dnmstats.turn')){
                    Log::channel('dnmstats')->notice("Response from pixel fired: $response");
                }
                curl_close($ch);
            }
        }
    }

}
