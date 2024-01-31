<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardCardsRequest;
use App\Http\Requests\DashboardRequest;
use App\Models\CardSiteItems;
use App\Models\ClickCard;
use App\Models\CreditCardAudit;
use App\Models\DecisionAudit;
use App\Models\Flow;
use App\Models\LogReapplySearch;
use App\Models\ReapplyAudit;
use App\Models\Sites;
use App\Models\VisitorCard;
use App\Services\DashboardStatisticService;
use Carbon\Carbon;
use App\Models\Visitor;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable|\Inertia\Response
     */
    public function index()
    {
        $forms = User::join('sites','sites.form_id','=','users.id')->whereNotNull('sites.id')->get(['users.id', 'users.name']);
        $flows = Flow::all();
        $phpTimeFormat = 'm/d/Y';
        $jsTimeFormat = 'MM/dd/yyyy';

        // return Inertia::render('Home', ['forms' => $forms, 'flows' => $flows]);
        return view('dashboard.homepage', [
            'forms' => $forms,
            'flows' => $flows,
            'phpTimeFormat' => $phpTimeFormat,
            'jsTimeFormat' => $jsTimeFormat
        ]);
    }

    public function dashboard(DashboardRequest $request)
    {
        $start = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d 00:00:00');
        $stop = Carbon::createFromFormat('m/d/Y', $request->stop)->format('Y-m-d 23:59:59');
        $leadType = $request->leadType;
        $actionType = $request->actionType;
        $flowId = $request->flowId;
        $formId = $request->formId;

        $database = config('database.connections.mysql.database');
        $databaseAudit = config('database.connections.mysql_audit.database');

        $user = Auth()->user();
        $isAdmin = $user->hasRole('admin');

        $filterUser = '';
        $filterFlow = '';
        $form = '';
        $theme = '';
        $leadTypeQuery = '';
        $actionTypeQuery = '';
        $themeJoin = '';
        $userJoin = '';

        if (!$isAdmin) {
            $filterUser = " AND u.id =" . $user->id;
            $userJoin = "LEFT JOIN $database.users AS u ON u.id = r.referral_id";
        }

        if($flowId){
            $filterFlow = " AND r.flow_id =" . $flowId;
        }

        if (is_numeric($formId) && $formId != 0) {
            $form = " AND r.referral_id =" . $formId;
        }

        if (!is_numeric($formId) && $formId != 0) {
            $themeJoin = "LEFT JOIN $database.sites AS s ON s.form_id = r.referral_id";
            $theme = " AND s.theme ='" . $formId . "'";
        }

        if ($leadType != 'all') {
            $leadTypeQuery = " AND r.lead_type = '$leadType'";
        }

        if ($actionType != 'all') {
            $actionTypeQuery = " AND r.action_type = '$actionType'";
        }

        $max_risk = config('services.ip_quality_score.max_value_risk');

//        $sql = DB::raw("
//                    SELECT COUNT(*) AS submits,
//                           SUM(IF(d.decision_status = 'sold', 1, 0)) AS solds,
//                           SUM(IF(d.decision_status = 'error', 1, 0)) AS dnm_errors,
//                           SUM(IF(r.denied_mark, 1, 0)) AS denied_total,
//                           SUM(IF(d.redirected = 1, 1, 0)) AS redirects,
//                           SUM(d.decision_price) AS total_earnings,
//                           SUM(d.decision_price) AS income,
//                           SUM(IF(d.decision_price >= 2, d.decision_price, 0)) AS earnings_more_two,
//                           IF(SUM(IF(d.decision_status = 'sold', 1, 0)) != 0, SUM(IF(d.decision_price >= 2, d.decision_price, 0)) / SUM(IF(d.decision_status = 'sold', 1, 0)), 0) AS epl_with,
//                           IF(SUM(IF(d.decision_status = 'sold', 1, 0)) != 0, SUM(d.decision_price) / SUM(IF(d.decision_status = 'sold', 1, 0)), 0 ) AS epl_without,
//                           AVG(r.risk) AS average_risk,
//                           SUM(IF(r.risk > $max_risk, 1, 0)) AS risk_submits
//                    FROM $databaseAudit.reapplies AS r
//                        LEFT JOIN $databaseAudit.decisions AS d ON r.id = d.reapply_id
//                        $userJoin
//                        $themeJoin
//                    WHERE d.decision_status != 'test' AND r.created_at BETWEEN '$start' AND '$stop' $filterUser $filterFlow $form $theme $leadTypeQuery $actionTypeQuery");

        $dashboard = DB::selectOne(DB::raw("
                    SELECT COUNT(*) AS submits,
                           SUM(IF(d.decision_status = 'sold', 1, 0)) AS solds,
                           SUM(IF(d.decision_status = 'error', 1, 0)) AS dnm_errors,
                           SUM(IF(r.denied_mark, 1, 0)) AS denied_total,
                           SUM(IF(d.redirected = 1, 1, 0)) AS redirects,
                           SUM(d.decision_price) AS total_earnings,
                           SUM(d.decision_price) AS income,
                           SUM(IF(d.decision_price >= 2, d.decision_price, 0)) AS earnings_more_two,
                           IF(SUM(IF(d.decision_status = 'sold', 1, 0)) != 0, SUM(IF(d.decision_price >= 2, d.decision_price, 0)) / SUM(IF(d.decision_status = 'sold', 1, 0)), 0) AS epl_with,
                           IF(SUM(IF(d.decision_status = 'sold', 1, 0)) != 0, SUM(d.decision_price) / SUM(IF(d.decision_status = 'sold', 1, 0)), 0 ) AS epl_without,
                           AVG(r.risk) AS average_risk,
                           SUM(IF(r.risk > $max_risk, 1, 0)) AS risk_submits,
                           SUM(IF(r.request_id_mark, 1, 0)) AS request_id_mark,
                           SUM(IF(r.risk > $max_risk, 1, 0)) AS risk_submits,
                           SUM(r.cookie_mark) AS cookie_submits
                    FROM $databaseAudit.reapplies AS r
                        LEFT JOIN $databaseAudit.decisions AS d ON r.id = d.reapply_id
                        $userJoin
                        $themeJoin
                    WHERE d.decision_status != 'test' AND r.created_at BETWEEN '$start' AND '$stop' $filterUser $filterFlow $form $theme $leadTypeQuery $actionTypeQuery"));

        $data = [];
        $submits = $dashboard->submits;
        $solds = $dashboard->solds;
        $redirects = $dashboard->redirects;
        $totalVisits = $isAdmin ? Visitor::sum('visits_amount') : Visitor::where('referral_id', '=', $user->id)->sum('visits_amount');
        $uniqueVisits = $isAdmin ? Visitor::count('visits_amount') : Visitor::where('referral_id', '=', $user->id)->count('visits_amount');

        $totalIncome = $isAdmin ? DecisionAudit::where('redirected', '=', 1)->sum('decision_price') : DecisionAudit::where('redirected', '=', 1)->join('reapplies', 'reapplies.id', '=', 'decisions.reapply_id')->where('reapplies.referral_id', '=', $user->id)->sum('decision_price');

        if ($isAdmin) {
            $visitsQuery = Visitor::whereBetween('visitors.created_at', [$start, $stop])
                ->when($formId, function ($query, $formId) {
                    return is_numeric($formId) ? $query->where('visitors.referral_id', '=', $formId) : $query->join('sites','referral_id','=','sites.form_id')->where('sites.theme', '=', $formId);
                })
                ->when($flowId, function ($query, $flowId) {
                    return $query->where('visitors.flow_id', '=', $flowId);
                });
            $visits = $visitsQuery->sum('visits_amount');
            $clicksCookie = $visitsQuery->sum('cookie_mark');
            $clicks = Visitor::whereBetween('visitors.created_at', [$start, $stop])
                ->when($formId, function ($query, $formId) {
                    return is_numeric($formId) ? $query->where('visitors.referral_id', '=', $formId) : $query->join('sites','referral_id','=','sites.form_id')->where('sites.theme', '=', $formId);
                })
                ->when($flowId, function ($query, $flowId) {
                    return $query->where('visitors.flow_id', '=', $flowId);
                })
                ->when($actionType, function ($query, $actionType) {
                    if($actionType == 'all'){
                        return $query;
                    }
                    return $query->where('visitors.action_type',$actionType);
                })
                ->sum('clicks_amount');
        } else {
            $visitsQuery = Visitor::whereBetween('created_at', [$start, $stop])
                ->where('referral_id', '=', $user->id)
                ->sum('visits_amount');
            $visits = $visitsQuery->sum('visits_amount');
            $clicksCookie = $visitsQuery->sum('cookie_mark');
            $clicks = Visitor::whereBetween('created_at', [$start, $stop])
                ->where('referral_id', '=', $user->id)
                ->when($flowId, function ($query, $flowId) {
                    return $query->where('visitors.flow_id', '=', $flowId);
                })
                ->when($actionType, function ($query, $actionType) {
                    if($actionType == 'all'){
                        return $query;
                    }
                    return $query->where('visitors.action_type',$actionType);
                })
                ->sum('clicks_amount');
        }
        $income = $dashboard->income;

        $clicksToSubmits = $clicks != 0 ? $submits * 100 / $clicks : 0;
        $soldsToSubmits = $submits != 0 ? $solds * 100 / $submits : 0;
        $redirectRate = $solds != 0 ? $redirects * 100 / $solds : 0; // changed to solds instead submits
        $visitsToClicks = $visits != 0 ? $clicks * 100 / $visits : 0;

        $epcTotal = $totalVisits == 0 ? 0 : $totalIncome / $totalVisits;
        $epcPeriod = $visits == 0 ? 0 : $income / $visits;


        $LogReapplySearchEmail = LogReapplySearch::whereBetween('logs_reapply_search.created_at', [$start, $stop])
            ->where('search_type','email');
        $LogReapplySearchPhone = LogReapplySearch::whereBetween('logs_reapply_search.created_at', [$start, $stop])
            ->where('search_type','phone');

        if (is_numeric($formId) && $formId != 0) {
            $LogReapplySearchEmail->where('user_id',$formId);
            $LogReapplySearchPhone->where('user_id',$formId);
        }

        if (!is_numeric($formId) && $formId != 0) {
            $LogReapplySearchEmail->join('sites', 'logs_reapply_search.user_id', '=', 'sites.form_id')
                ->where('sites.theme',$formId);
            $LogReapplySearchPhone->join('sites', 'logs_reapply_search.user_id', '=', 'sites.form_id')
                ->where('sites.theme',$formId);
        }

        $data['search_reapply_email_total'] = $LogReapplySearchEmail->count();
        $data['search_reapply_email_found'] = $LogReapplySearchEmail->sum('found');
        $data['search_reapply_phone_total'] = $LogReapplySearchPhone->count();
        $data['search_reapply_phone_found'] = $LogReapplySearchPhone->sum('found');

        $data['total_leads'] = ReapplyAudit::whereBetween('created_at', [$start, $stop])->count();

        $data['total_income'] = $dashboard->income ?? 0;
        $data['clicks_in_period'] = $clicks;
        $data['submits_in_period'] = $submits;
        $data['clicks_to_sub'] = round($clicksToSubmits, 2);
        $data['total_earnings'] = $totalIncome;

        $data['epc']['all_time'] = round($epcTotal, 2);
        $data['epc']['period'] = round($epcPeriod, 2);

        $data['redirectRate'] = round($redirectRate ?? 0, 2);
        $data['redirects'] = round($redirects, 2);
        $data['total_visits'] = $totalVisits;
        $data['unique_visits'] = $uniqueVisits;
        $data['sold_to_submit'] = round($soldsToSubmits, 2);
        $data['visits_to_clicks'] = round($visitsToClicks, 2);

        $data['solds_in_period'] = empty($solds) ? 0 : $solds;
        $data['visits_in_period'] = $visits;

        $data['epl']['with'] = round($dashboard->epl_with, 2);
        $data['epl']['without'] = round($dashboard->epl_without, 2);

        $data['dnm_errors'] = $dashboard->dnm_errors ?? 0;
        $data['denied_total'] = $dashboard->denied_total ?? 0;

        $data['average_risk'] = round($dashboard->average_risk, 2) ?? 0;
        $data['risk_submits'] = $dashboard->risk_submits ?? 0;

        $data['request_id_mark'] = $dashboard->request_id_mark ?? 0;

        $data['cookie_clicks'] = $clicksCookie;
        $data['cookie_submits'] = $dashboard->cookie_submits ?? 0;

        return response()->json($data);
    }

    public function cards()
    {
        $forms = Sites::leftJoin('users', 'sites.form_id', '=', 'users.id')->where('site_type', 'card')->get();
        // return Inertia::render('Cards', ['forms' => $forms]);
        return view('dashboard.cards', ['forms' => $forms]);
    }

    public function get_cards(DashboardCardsRequest $request)
    {
        $start = Carbon::createFromFormat('d/m/Y', $request->start)->format('Y-m-d 00:00:00');
        $stop = Carbon::createFromFormat('d/m/Y', $request->stop)->format('Y-m-d 23:59:59');

        $user = Auth()->user();
        $isAdmin = $user->hasRole('admin');
        $data = [];

        $totalClicksPeriod = $request->formId ?
            ClickCard::where('referral_id', '=', $request->formId)
            ->whereBetween('created_at', [$start, $stop])
            ->sum('click_amount') :
            ClickCard::whereBetween('created_at', [$start, $stop])
                ->sum('click_amount');
        $uniqueClicksPeriod = $request->formId ?
            ClickCard::where('referral_id', '=', $request->formId)
                ->whereBetween('created_at', [$start, $stop])
                ->count('click_amount') :
            ClickCard::whereBetween('created_at', [$start, $stop])
                ->count();

        $visitsAmount = $request->formId ?
            VisitorCard::where('referral_id', '=', $request->formId)
                ->whereBetween('created_at', [$start, $stop])
                ->sum('visits_amount') :
            VisitorCard::whereBetween('created_at', [$start, $stop])
                ->sum('visits_amount');
        $submitsAmount = $request->formId ?
            CreditCardAudit::where('referral_id', '=', $request->formId)
                ->whereBetween('created_at', [$start, $stop])->count() :
            CreditCardAudit::whereBetween('created_at', [$start, $stop])
                ->count();

        $visitsToSubmits = $submitsAmount != 0 ? $submitsAmount * 100 / $visitsAmount : 0;
        $totalClicks = $isAdmin ? ClickCard::sum('click_amount') : 0;
        $uniqueClicks = $isAdmin ? ClickCard::count('click_amount') : 0;

        if ( $request->formId ) {
            $items = CardSiteItems::select('sites.domain_name', 'card_site_items.name', 'card_site_items.id AS item_id', 'sites.id', 'card_site_items.site_id','sites.form_id')
                ->leftJoin('sites','sites.id','=','card_site_items.site_id')
                ->withTrashed()
                ->where('sites.form_id',$request->formId)
                ->get()
                ->toArray();
            foreach ($items as &$item) {
                $item['total_clicks'] = ClickCard::where('card_site_item_id',$item['item_id'])
                    ->whereBetween('created_at', [$start, $stop])
                    ->sum('click_amount');
                $item['uniq_clicks'] = ClickCard::where('card_site_item_id',$item['item_id'])
                    ->whereBetween('created_at', [$start, $stop])
                    ->count('click_amount');
            }
        } else {
            $items = CardSiteItems::select('sites.domain_name', 'card_site_items.name', 'card_site_items.id AS item_id', 'sites.id', 'card_site_items.site_id')
                ->leftJoin('sites','sites.id','=','card_site_items.site_id')
                ->withTrashed()
                ->get()
                ->toArray();
            foreach ($items as &$item) {
                $item['total_clicks'] = ClickCard::where('card_site_item_id',$item['item_id'])
                    ->whereBetween('created_at', [$start, $stop])
                    ->sum('click_amount');
                $item['uniq_clicks'] = ClickCard::where('card_site_item_id',$item['item_id'])
                    ->whereBetween('created_at', [$start, $stop])
                    ->count('click_amount');
            }
        }

        $data['totalClicksPeriod'] = $totalClicksPeriod;
        $data['uniqueClicksPeriod'] = $uniqueClicksPeriod;
        $data['visits'] = $visitsAmount;
        $data['submits'] = $submitsAmount;
        $data['visitsToSubmits'] = round($visitsToSubmits, 2);
        $data['totalClicks'] = $totalClicks;
        $data['uniqueClicks'] = $uniqueClicks;

        $data['items'] = $items;

        return response()->json($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function dashboardChart(DashboardRequest $request, DashboardStatisticService $dashboardStatisticService)
    {
        $start = Carbon::createFromFormat('m/d/Y', $request->start);
        $stop = Carbon::createFromFormat('m/d/Y', $request->stop);

        $leadType = $request->leadType;
        $actiontype = $request->actionType;
        $flowId = $request->flowId;
        $formId = $request->formId;

        $data = $dashboardStatisticService::chart($start, $stop, $leadType, $actiontype, $formId, $flowId);

        return response()->json($data);
    }
}
