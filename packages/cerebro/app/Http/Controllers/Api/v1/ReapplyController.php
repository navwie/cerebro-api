<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\ApiCheckPhoneRequest;
use App\Http\Requests\ApiCheckStatusRequest;
use App\Http\Requests\ApiGetStartedRequest;
use App\Http\Requests\ApiReapplyGetRequest;
use App\Http\Requests\ApiUnsubscribeRequest;
use App\Models\CustomerAudit;
use App\Models\CustomerState;
use App\Models\CustomersToVisitors;
use App\Models\DecisionAudit;
use App\Models\LogApiTime;
use App\Models\LogPixel;
use App\Models\LogReapplySearch;
use App\Models\Reapply;
use App\Models\Decision;
use App\Models\Customer;
use App\Models\Bank;
use App\Models\CustomerToBank;
use App\Models\ReapplyAudit;
use App\Models\SsnRequests;
use App\Models\Visitor;
use App\Services\DnmService;
use App\Services\IpqualityscoreService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiReapplyResource;
use App\Http\Resources\ReapplyFormResource;
use App\Http\Resources\MainFormResource;
use App\Http\Requests\ApiReapplyStoreRequest;
use App\Http\Requests\ApiReapplyUpdateRequest;
use App\Http\Requests\ApiReapplySearchRequest;
use App\Http\Requests\ApiMainSearchRequest;
use App\Http\Requests\ApiMainUpdateRequest;
use App\Http\Requests\ApiReapplyImportRequest;
use App\Models\User;
use App\Services\FocusMarketingService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ReapplyController extends Controller
{
    private $requestAttempts;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->throttleKey = config('dnm.ssnMiddleware.prefixKey') . $request->input('ip_address');
        $requestAttempts = 0;
    }

    /**
     * Create resource
     *
     * @param ApiReapplyStoreRequest $request
     * @param DnmService $dnm
     * @param IpqualityscoreService $ipqualityscoreService
     * @return JsonResponse
     */
    public function store(ApiReapplyStoreRequest $request, DnmService $dnm, IpqualityscoreService $ipqualityscoreService)
    {
        $data = $request->all();
        $data['action_type'] = 'full';
        if ($data['lead_type'] == User::TYPE_SOURCE_LEAD && $data['auto_redirect_to_lead']) {
            $personal_to_lead = Customer::TYPE_INCOME_PERSONAL_TO_LEAD;
            $data['income_type'] = $personal_to_lead[$data['income_type']];
            $user = auth()->user();
            $data['requested_amount'] = $user->post_back_amount;
        } elseif ($data['lead_type'] == User::TYPE_SOURCE_PERSONAL && $data['auto_redirect_to_lead']) {
            $lead_to_personal = Customer::TYPE_INCOME_LEAD_TO_PERSONAL;
            $data['income_type'] = $lead_to_personal[$data['income_type']];
        }
        $data = array_merge($data,$ipqualityscoreService::checkIp($data['ip_address'],$data['user_agent']));
        CustomerState::where('email',$data['email'])->delete();
        $customer = Customer::create($data);
        $data['log_id'] = $customer->audits()->latest()->first()->id;
        $customerAudit = CustomerAudit::create($data);
        $bank = Bank::firstOrCreate(['routing_number' => $data['routing_number']], $data);
        $data['bank_id'] = $bank['id'];
        $data['customer_id'] = $customer['id'];
        $data['referral_id'] = auth()->user()->id;

        CustomerToBank::create($data);
        $reapply = Reapply::create($data);

        $data['log_id'] = $reapply->audits()->latest()->first()->id;
        $data['customer_id'] = $customerAudit->id;
        $reapplyAudit = ReapplyAudit::create($data);
        $data['reapply_audit_id'] = $reapplyAudit->id;
        $data['customer_audit_id'] = $customerAudit->id;
        $this->saveCustomerToVisitor($request,$reapply,'new customer');

        $data['action_type'] = 'new';
        return $this->generateResponse($reapply,$data,$dnm);
    }

    /**
     * Update whole resource
     *
     * @param ApiMainUpdateRequest $request
     * @param DnmService $dnm
     * @param IpqualityscoreService $ipqualityscoreService
     * @return JsonResponse
     */
    public function update_main(ApiMainUpdateRequest $request, DnmService $dnm, IpqualityscoreService $ipqualityscoreService): JsonResponse
    {
        $data = $request->all();
        $reapply = Reapply::find($data['reapply_id']);
        $data['action_type'] = 'full';
        try{
            if ($data['lead_type'] == User::TYPE_SOURCE_LEAD && $data['auto_redirect_to_lead']) {
                $personal_to_lead = Customer::TYPE_INCOME_PERSONAL_TO_LEAD;
                $data['income_type'] = $personal_to_lead[$data['income_type']];
                $reapply->customer->income_type = $data['income_type'];
                $user = auth()->user();
                $data['requested_amount'] = $user->post_back_amount;
            } elseif ($data['lead_type'] == User::TYPE_SOURCE_PERSONAL && $data['auto_redirect_to_lead']) {
                $lead_to_personal = Customer::TYPE_INCOME_LEAD_TO_PERSONAL;
                $data['income_type'] = $lead_to_personal[$data['income_type']];
                $reapply->customer->income_type = $data['income_type'];
            }
        } catch( \Exception $e){
            $user = auth()->user();
            $data['requested_amount'] = $user->post_back_amount;
            $data['income_type'] = 'Job Income';
            $reapply->customer->income_type = $data['income_type'];
            if(config('logging.channels.update_error.turn')) {
                Log::channel('update_error')->notice('line ' . $e->getLine() . ' :' . $e->getMessage());
                Log::channel('update_error')->notice(json_encode($data));
            }
        }

        if (empty($reapply) || empty($reapply->toArray())) {
            return response()->xml(['message' => 'No data found'], 200, [], 'response', 'utf-8');
        } else {
            $this->clearAttemptsSsn($request);
            $data = array_merge($data,$ipqualityscoreService::checkIp($data['ip_address'],$data['user_agent']));
            $data['referral_id'] = auth()->user()->id;

            $reapply->customer->update($data);
            $data['log_id'] = $reapply->customer->audits()->latest()->first()->id;
            $customerAudit = CustomerAudit::create($data);

            $reapply->update($data);
            $data['log_id'] = $reapply->audits()->latest()->first()->id;
            $data['customer_id'] = $customerAudit->id;
            $reapplyAudit = ReapplyAudit::create($data);
            $data['reapply_audit_id'] = $reapplyAudit->id;
            $data['customer_audit_id'] = $customerAudit->id;

            $bank = Bank::firstOrCreate(['routing_number' => $data['routing_number']], $data);
            $data['bank_id'] = $bank->id;
            $data['customer_id'] = $reapply->customer->id;
            $reapply->customer->bank_info->update($data);

            $this->saveCustomerToVisitor($request,$reapply,'refill');
            $data['action_type'] = 'refill';
            return $this->generateResponse($reapply, $data, $dnm);
        }
    }

    /**
     *  Update reapply part resource
     *
     * @param ApiReapplyUpdateRequest $request
     * @param DnmService $dnm
     * @param IpqualityscoreService $ipqualityscoreService
     * @return JsonResponse
     */
    public function update_reapply(ApiReapplyUpdateRequest $request, DnmService $dnm, IpqualityscoreService $ipqualityscoreService): JsonResponse
    {
        $data = $request->all();
        $data['action_type'] = 'reapply';
        $reapply = Reapply::whereHas('customer', function ($query) use ($request) {
            $query->where('email', '=', $request->email);
            $query->where('ssn_short', '=', openssl_encrypt($request->ssn, 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv')));
            return $query;
        })->latest()->first();
        if(!(empty($reapply))){
            if(config('logging.channels.reapply_request.turn')) {
                Log::channel('reapply_request')->notice(json_encode($reapply->toArray()));
            }
        }else{
            if(config('logging.channels.reapply_request.turn')) {
                Log::channel('reapply_request')->notice(json_encode('not found reapply'));
            }
        }
        if (empty($reapply) || empty($reapply->toArray())) {
            return response()->json(['message' => 'No data found'], 422);
        } else {
            $this->clearAttemptsSsn($request);
            $data = array_merge($data,$ipqualityscoreService::checkIp($data['ip_address'],$data['user_agent']));

            $income_type = ucwords($reapply->customer->income_type);
            if ($data['lead_type'] == User::TYPE_SOURCE_LEAD) {
                $personal_to_lead = Customer::TYPE_INCOME_PERSONAL_TO_LEAD;
                if (isset($personal_to_lead[$income_type])) {
                    $reapply->customer->income_type = $personal_to_lead[$income_type];
                    $data['income_type'] = $personal_to_lead[$income_type];
                }
                if ($data['auto_redirect_to_lead']) {
                    $user = auth()->user();
                    $data['requested_amount'] = $user->post_back_amount;
                }
            } elseif ($data['lead_type'] == User::TYPE_SOURCE_PERSONAL) {
                $lead_to_personal = Customer::TYPE_INCOME_LEAD_TO_PERSONAL;
                if (isset($lead_to_personal[$income_type])) {
                    $reapply->customer->income_type = $lead_to_personal[$income_type];
                    $data['income_type'] = $lead_to_personal[$income_type];
                }
            }
            $data['referral_id'] = auth()->user()->id;
            $data['imported_mark'] = 0;
            $data['ssn'] = $reapply->customer->ssn;

            $reapply->customer->update($data);
            $customerAuditsFirst = $reapply->customer->audits()->latest()->first();
            $data['log_id'] = $customerAuditsFirst == null ? $customerAuditsFirst : $customerAuditsFirst->id;
            $customerAudit = CustomerAudit::create($data + $reapply->customer->toArray());

            $reapply->update($data);
            $reapplyAuditsFirst = $reapply->audits()->latest()->first();
            $data['log_id'] = $reapplyAuditsFirst == null ? $reapplyAuditsFirst : $reapplyAuditsFirst->id;
            $data['customer_id'] = $customerAudit->id;
            $reapplyAudit = ReapplyAudit::create($data + $reapply->toArray());
            $data['reapply_audit_id'] = $reapplyAudit->id;
            $data['customer_audit_id'] = $customerAudit->id;

            $data['bank_id'] = $reapply->customer->bank_info->bank->id;
            $data['customer_id'] = $reapply->customer_id;

            $reapply->customer->bank_info->update($data);
            $this->saveCustomerToVisitor($request,$reapply,'reapply');
            $data['action_type'] = 'reapply';
            return $this->generateResponse($reapply, $data, $dnm);
        }
    }

    public function validation_store(ApiReapplyStoreRequest $request)
    {
        return response()->json([
            "success" => 1,
            "request_id" =>  hash('sha512', time() . Str::random())
        ]);
    }

    public function validation_update_reapply(ApiReapplyUpdateRequest $request)
    {
        $reapply = Reapply::whereHas('customer', function ($query) use ($request) {
            $query->where('email', '=', $request->email);
            $query->where('ssn_short', '=', openssl_encrypt($request->ssn, 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv')));
            return $query;
        })->latest()->first();
        if (empty($reapply) || empty($reapply->toArray())) {
            return response()->json(['message' => 'No data found'], 422);
        } else {
            return response()->json([
                "success" => 1,
                "request_id" =>  hash('sha512', time() . Str::random())
            ]);
        }
    }

    public function validation_update_main(ApiMainUpdateRequest $request)
    {
        $data = $request->all();
        $reapply = Reapply::find($data['reapply_id']);
        if (empty($reapply) || empty($reapply->toArray())) {
            return response()->json(['message' => 'No data found'], 422);
        } else {
            return response()->json([
                "success" => 1,
                "request_id" =>  hash('sha512', time() . Str::random())
            ]);
        }
    }

    /**
     * Check status of decision
     *
     * @param ApiCheckPhoneRequest $request
     * @return JsonResponse
     */
    public function check_phone(ApiCheckPhoneRequest $request): JsonResponse
    {
        $customers = Customer::where([
            ['home_phone', $request->home_phone],
            ['first_name', $request->first_name],
        ]);
        $count = $customers->count();
        $user = auth()->user();
        $reapplySearchLogData = [
            'user_id' => $user->id,
            'search_type' => 'phone',
            'url' => $request->url,
        ];
        if ($count == 0) {
            $reapplySearchLogData['found'] = 0;
            $reapplyRes = [
                'message' => 'No data found',
                'status' => 'not found',
            ];
        } elseif ($count == 1) {
            $reapplySearchLogData['found'] = 1;
            $customer = $customers->first();
            $reapply = Reapply::where('customer_id', $customer->id)->first();
            $reapplyRes = new ReapplyFormResource($reapply);
        } else {
            $reapplySearchLogData['found'] = 0;
            $reapplyRes = [
                'message' => 'No data found',
                'status' => 'not found',
            ];
        }
        LogReapplySearch::create($reapplySearchLogData);
        return response()->json($reapplyRes);
    }

    /**
     * Check status of decision
     *
     * @param ApiCheckStatusRequest $request
     * @return JsonResponse
     */
    public function check_status(ApiCheckStatusRequest $request): JsonResponse
    {
        $reapply = Reapply::whereHas('customer', function ($query) use ($request) {
            $query->where('email', '=', $request->email);
            $ssn = $request->ssn;
            if(strlen($ssn) == 4){
                $query->where('ssn_short', '=', openssl_encrypt($request->ssn, 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv')));

            }elseif(strlen($ssn) == 9){
                $query->where('ssn', '=', openssl_encrypt($request->ssn, 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv')));
            }
            return $query;
        })->latest()->first();

        if (empty($reapply)) {
            return response()->json(['message' => 'No data found'], 432);
        }
        $decision = Decision::where(['customer_id' => $reapply->customer_id])->where('updated_at','>=',$reapply->updated_at)->first();
        $decisionAudit = DecisionAudit::where(['customer_id' => $reapply->customer_id])->where('updated_at','>=',$reapply->updated_at)->first();
        if (empty($decision) || empty($decisionAudit)) {
            return response()->json(['message' => 'No data found'], 432);
        }
        return response()->json([
            'status' => $decision->decision_status,
            'redirect' => $decision->decision_redirect,
            'decision_id' => $decision->id,
            'reapply_id' => $reapply->id,
        ],200);
    }

    public function check_status_decision(Request $request): JsonResponse
    {
        if($request->click_id){
           LogApiTime::where('click_id',$request->click_id)
               ->latest()
               ->limit(1)
               ->update([
                   'last_check_status_decision' => now(),
                   'updated_at' => DB::raw('updated_at')
           ]);
        }
        $reapply = ReapplyAudit::where([
            'request_id' => $request->request_id,
        ])->first();
        if (empty($reapply)) {
            return response()->json(['message' => 'No data found']);
        }
        $decisionAudit = DecisionAudit::where(['reapply_id' => $reapply->id])->latest()->first();
        if (empty($decisionAudit)) {
            $response = [];
            if ($this->check_timeout($reapply)) {
                $response['timeout'] = 1;
            }
            $response['message'] = 'No data found';
            return response()->json($response);
        }
        $reapply = Customer::where('email',$request->email)->first()->reapply->first();
        return response()->json([
            'status' => $decisionAudit->decision_status,
            'redirect' => $decisionAudit->decision_redirect,
            'decision_id' => $decisionAudit->hash_id,
            'reapply_id' => $reapply->id,
        ]);
    }

    private function check_timeout($reapply)
    {
        $user = User::find($reapply->referral_id);
        $timeDiff = time() - $reapply->updated_at->timestamp;
        if ($timeDiff > ($user->processing_time ?? 300)) {
            Log::channel('debugging_timeout')->notice('timediff: ' . $timeDiff . '|' . 'reapply:' . json_encode($reapply->toArray()) . '|' . 'processing time:' . $user->processing_time . '|' . 'request id:' . $reapply->request_id);
            return true;
        } else {
            return false;
        }
    }

    public function check_decision(Request $request): JsonResponse
    {
        $reapply = ReapplyAudit::where([
            'request_id' => $request->request_id,
        ])->first();

        $decisionAudit = DecisionAudit::where('reapply_id', $reapply->id)
            ->where('decision_status', 'sold')
            ->where('redirected', 0)
            ->where('created_at', '>=', Carbon::now()->subHours(2)->toDateTimeString())
            ->first();

        if (empty($decisionAudit)) {
            return response()->json([
                'status' => 0,
                'message' => 'Not found',
            ]);
        } else {
            return response()->json([
                'status' => 1,
                'decision_id' => $decisionAudit->hash_id,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id): Response
    {
        $isDeleted = Reapply::destroy($id);
        return response()->xml([
            'message' => 'Reapply was suceccfully deleted',
            'isDeleted' => $isDeleted
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ApiUnsubscribeRequest $request
     * @return JsonResponse|void
     */
    public function unsubscribe(ApiUnsubscribeRequest $request)
    {
        $reapply = Reapply::whereHas('customer', function ($query) use ($request) {
            $query->where('email', '=', $request->email);
            return $query;
        })->first();
        if (empty($reapply)) {
            return response()->json(['errors' => ['email' => ['No data found']]], 422);
        }
        $reapply->customer->bank_info->delete();
        $reapply->customer->delete();
        $reapply->delete();
    }

    /**
     * Display the specified resource by request user data.
     *
     * @param ApiReapplySearchRequest $request
     * @return JsonResponse
     */
    public function search_reapply(ApiReapplySearchRequest $request): JsonResponse
    {
        $reapply = Reapply::withTrashed()->whereHas('customer', function ($query) use ($request) {
            $query->withTrashed()->where('email', '=', $request->email);
            return $query;
        })->first();

        $this->saveCustomerToVisitor($request,$reapply,'check email');
        $user = auth()->user();
        $reapplySearchLogData = [
            'user_id' => $user->id,
            'search_type' => 'email',
            'url' => $request->url,
        ];

        if (empty($reapply)) {
            $response = [
                'message' => 'No data found',
                'status' => 'not found',
            ];
            $reapplySearchLogData['found'] = 0;

            LogReapplySearch::create($reapplySearchLogData);
            return response()->json($response);
        }

        if($reapply->trashed()){
            $response = [
                'message' => 'This email has been deleted, please contact support'
            ];
            $reapplySearchLogData['found'] = 0;

            LogReapplySearch::create($reapplySearchLogData);
            return response()->json($response,301);
        }

        $this->clearAttemptsSsn($request);
        $response = new ReapplyFormResource($reapply);
        $reapplySearchLogData['found'] = 1;

        LogReapplySearch::create($reapplySearchLogData);
        return response()->json($response);
    }

    /**
     * Display the specified resource by request user data.
     *
     * @param ApiReapplySearchRequest $request
     * @return JsonResponse
     */
    public function get_reapply(ApiReapplyGetRequest $request): JsonResponse
    {
        $reapply = Reapply::withTrashed()->whereHas('customer', function ($query) use ($request) {
            $query->withTrashed()->where('email', '=', $request->email);
            return $query;
        })->first();

        if (empty($reapply)) {
            $response = [
                'message' => 'No data found',
                'status' => 'not found',
            ];

            return response()->json($response);
        }

        if($reapply->trashed()){
            $response = [
                'message' => 'This email has been deleted, please contact support'
            ];

            return response()->json($response,301);
        }

        $this->clearAttemptsSsn($request);
        $response = new ReapplyFormResource($reapply);

        return response()->json($response);
    }

    /**
     * Display the specified resource by request user data.
     *
     * @param ApiMainSearchRequest $request
     * @return JsonResponse
     */
    public function search_main(ApiMainSearchRequest $request): JsonResponse
    {
        $reapply = Reapply::whereHas('customer', function ($query) use ($request) {
            $query->where('email', '=', $request->email);
            $query->where('ssn_short', '=', openssl_encrypt($request->ssn, 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv')));
            return $query;
        })->latest()->first();

        if (empty($reapply)) {
            return response()->json(['message' => 'No data found'], 422);
        }

        $this->clearAttemptsSsn($request);
        $reapplyRes = new MainFormResource($reapply);
        return response()->json($reapplyRes);
    }

    /**
     * Display the specified resource by request user data.
     *
     * @param ApiGetStartedRequest $request
     * @return JsonResponse
     */
    public function get_started(ApiGetStartedRequest $request): JsonResponse
    {
        $reapply = Reapply::whereHas('customer', function ($query) use ($request) {
            $query->where('email', '=', $request->email);
            return $query;
        })->latest()->first();

        if (empty($reapply)) {
            $this->clearAttemptsSsn($request);
            return response()->json(['message' => 'No data found'], 404);
        }

        if($reapply->customer->ssn_short == openssl_encrypt($request->ssn, 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv'))){
            $this->clearAttemptsSsn($request);
            $reapplyRes = new ReapplyFormResource($reapply);
            return response()->json($reapplyRes);
        }else{
            return response()->json(['message' => 'No data match'], 422);
        }
    }

    /**
     * Import
     *
     * @param ApiReapplyImportRequest $request
     * @return mixed
     */
    public function import(ApiReapplyImportRequest $request): mixed
    {
        $data = $request->all();
        $data['imported_mark'] = 1;
        $chanelData = array();
        $newFormData = [
            'name' => $data['source_name'],
            'menuroles' => 'user', 'password' => bcrypt(config('dnm.defaultFormSettings.password')),
            'post_back_amount' => config('dnm.defaultFormSettings.postBackAmount'), 'personal_min_req' => config('dnm.defaultFormSettings.personalMinReq'),
        ];
        if ($data['source_type'] == User::TYPE_SOURCE_PERSONAL) {
            $chanelData['personal_channel_id'] = $data['channel_id'];
            $chanelData['personal_password'] = $data['password'];
        } elseif ($data['source_type'] == User::TYPE_SOURCE_LEAD) {
            $chanelData['lead_channel_id'] = $data['channel_id'];
            $chanelData['lead_password'] = $data['password'];
        }
        $newFormData = array_merge($newFormData, $chanelData);
        $form = User::firstOrNew(['email' => $data['source_email']], $newFormData);
        if ($form->exists == false) {
            $form->save();
        } else {
            $form->update($chanelData);
        }
        $data['ssn_short'] = openssl_encrypt(substr($data['ssn'], -4), 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv'));
        $data['ssn'] = openssl_encrypt($data['ssn'], 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv'));
        $data['account_number'] = openssl_encrypt($data['account_number'], 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv'));
        $customer = Customer::firstOrNew(['email' => $data['email']], $data);
        if ($customer->exists == false) {
            $customer->saveQuietly();
        } else {
            $customer->updateQuietly($data);
        }
        $bank = Bank::firstOrNew(['routing_number' => $data['routing_number']], $data);
        if ($bank->exists == false) {
            $bank->saveQuietly();
        } else {
            $bank->updateQuietly($data);
        }
        $data['bank_id'] = $bank['id'];
        $data['customer_id'] = $customer['id'];
        $data['referral_id'] = $form->id;

        $ctb = CustomerToBank::firstOrNew(['customer_id' => $customer->id], $data);
        if ($ctb->exists == false) {
            $ctb->saveQuietly();
        } else {
            $ctb->updateQuietly($data);
        }
        $reapply = Reapply::firstOrNew(['customer_id' => $customer->id], $data);
        if ($reapply->exists == false) {
            $reapply->saveQuietly();
        } else {
            $reapply->updateQuietly($data);
        }
        $data['reapply_id'] = $reapply['id'];
        $decision = Decision::firstOrNew([
            'customer_id' => $customer->id
        ], $data);
        if ($decision->exists == false) {
            $decision->saveQuietly();
        } else {
            $decision->updateQuietly($data);
        }
        return response()->xml(['status' => 'accepted'], 200, [], 'request', 'utf-8');
    }

    /**
     * Prepare, send and store dnm request
     *
     * @param Reapply $reapply
     * @param array $formData
     * @param DnmService $dnm
     * @param integer $dnm
     * @return array
     */
    private function _processAndSend(Reapply $reapply, array $formData, DnmService $dnm, int $log_id): array
    {
        $leadType = $formData['lead_type'];
        $user = auth()->user();
        $url = $leadType == User::TYPE_SOURCE_LEAD ? config('dnm.urls.lead') : config('dnm.urls.personal');
        $reapply->channel_id = $leadType == User::TYPE_SOURCE_LEAD ? $user->lead_channel_id : $user->personal_channel_id;
        $reapply->password = $leadType == User::TYPE_SOURCE_LEAD ? $user->lead_password : $user->personal_password;
        $this->_setRequestAttempts();
        do {
            $reapply->min_price = $this->_setMinPrice($leadType, $this->requestAttempts);
            $reapplyRes = new ApiReapplyResource($reapply);

            $this->requestAttempts--;
            $res = response()->xml($reapplyRes->toArray(1), 200, [], 'request', 'utf-8');
            $response = $dnm->sendRequest($res->getContent(), $url, $reapply, $formData, $log_id);
        } while ($leadType == User::TYPE_SOURCE_LEAD && $this->requestAttempts > 0 && in_array($response['status'], array('reject', 'test')));
        $decision = Decision::saveDecision($reapply, $formData, $user->id, $response);
        $response['decision_id'] = $decision->id;

        if(!isset($response['fake_response'])){
            if(config('dnm.send_post_back_request')){
                if($response['status'] == 'sold'){
                    DnmService::postBackRequest($decision->decision_autdit_id);
                }
            }

            if(config('dnm.sendFM')){
                $fm = new FocusMarketingService();
                $fm->sendRequest($reapply, $response);
            }
        }

        return $response;
    }

    /**
     * Mark redirected specified resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function mark_redirected(Request $request): JsonResponse
    {
        Decision::markRedirected($request->input('id'), $request->input('request_id_mark'));
        return response()->json(['success' => true]);
    }

    /**
     * Mark denied specified resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function mark_denied(Request $request): JsonResponse
    {
        ReapplyAudit::where('request_id',$request->input('request_id'))
            ->latest()
            ->limit(1)
            ->update([
            'denied_mark' => 1,
        ]);
        return response()->json(['success' => true]);
    }

    /**
     *
     * @return JsonResponse
     */
    public function check_connection(): JsonResponse
    {
        return response()->json(['success' => true]);
    }

    public static function save_customer_state(Request $request)
    {
        if(Customer::where('email',$request->email)->first()){
            return response()->json([], 200);
        }
        $state = openssl_encrypt(json_encode($request->state),'aes-128-cbc',config('hashing.hashing_key'),0,config('hashing.hashing_iv'));

        $CustomerState = CustomerState::firstOrCreate(
            [
                'click_id' => $request->click_id,
                'email' => $request->email,
            ],
            [
                'referral_url' => $request->url,
                'state' => json_encode($state),
                'referral_id' => auth()->user()->id,
                'token' => Str::random(60),
            ]
        );

        if ($CustomerState->wasRecentlyCreated === false) {
            $CustomerState->update([
                'state' => json_encode($state),
            ]);
        }

        return response()->json([], 201);
    }

    public function change_action_type_visitor(Request $request){
        Visitor::find($request->visitor_id)->update([
            'action_type' => $request->action_type,
        ]);
        return response()->json(1, );
    }

    /**
     * @param Request $request
     */
    private function clearAttemptsSsn(Request $request)
    {
        RateLimiter::clear($this->throttleKey);
        SsnRequests::resetDelay($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_captcha(Request $request): JsonResponse
    {
        $captcha = app('captcha')->create('default', true);
        return response()->json(['captcha' => $captcha['img'], 'key' => $captcha['key']]);
    }

    /**
     *
     * @param $reapply
     * @param array $data
     * @param DnmService $dnm
     * @return JsonResponse
     */
    private function generateResponse($reapply, array $data, DnmService $dnm): JsonResponse
    {
        $data['reapply_id'] = $reapply->id;
        $data['customer_id'] = $reapply->customer->id;
        $log = LogPixel::create($data);
        $max_value = config('services.ip_quality_score.max_value_risk');

        if ($data['risk'] > $max_value) {
            $user = auth()->user();
            $response = [
                'status' => 'vpn_or_proxy',
                'decision_message' => 'ip validation failed'
            ];
            Decision::saveDecision($reapply, $data, $user->id, $response);
        } else {
            $sleep_duration = env('DNM_RESPONSE_SLEEP_DURATION',0);
            if($sleep_duration){
                sleep($sleep_duration);
            }
            $response = $this->_processAndSend($reapply, $data, $dnm, $log->id);
            $response['reapply_id'] = $reapply['id'];
        }
        return response()->json($response, 200);
    }

    /**
     * @param Request $request
     * @param Reapply|null $reapply
     * @param string $type
     */
    private function saveCustomerToVisitor(Request $request, Reapply|null $reapply, string $type)
    {
        if($request->click_id === 'null'){
            $request->click_id = null;
        }
        $visitor = Visitor::find($request->visitor_id);
        if(empty($visitor)){
            $visitor_id = 0;
        }else{
            $visitor_id = $visitor->id;
        }
        CustomersToVisitors::create([
            'customer_id' => empty($reapply) ? 0 : $reapply->customer_id,
            'visitor_id' => $visitor_id,
            'email' => $request->email,
            'ip' => $request->ip_address,
            'user_agent' => $request->input('user_agent'),
            'click_id' => $request->click_id,
            'type' => $type
        ]);
    }

    private function _setMinPrice($leadType, $attempt)
    {
        $user = auth()->user();
        $minPriceMap = !empty($user->lead_min_price) ? explode(',', $user->lead_min_price) : [];
        if($leadType == User::TYPE_SOURCE_LEAD && count($minPriceMap) > 0) {
            return $minPriceMap[count($minPriceMap) - $attempt];
        }
        return $user->min_price;
    }

    private function _setRequestAttempts()
    {
        $user = auth()->user();
        $minPriceMap = !empty($user->lead_min_price) ? explode(',', $user->lead_min_price) : [];
        $this->requestAttempts = count($minPriceMap);
    }
}
