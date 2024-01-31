<?php

namespace App\Http\Controllers;

use App\Services\DataTablesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    public function index()
    {
        return view('dashboard.statistic.statistic');
    }

    public function mainStatisticDatatables(Request $request, DataTablesService $dataTablesService)
    {
        $columns = [
            ['db' => 'name', 'where' => 'u.name', 'dt' => 0],
            ['db' => 'clicks', 'where' => 'clicks', 'dt' => 1],
            ['db' => 'total_leads', 'where' => 'total_leads', 'dt' => 2],
            ['db' => 'sold_leads', 'where' => 'sold_leads', 'dt' => 3],
            ['db' => 'epc', 'dt' => 4, 'formatter' => function ($d) {
                return round($d, 2);
                //return $d;
            }],
            ['db' => 'epl', 'dt' => 5, 'formatter' => function ($d) {
                return round($d, 2);
                //return $d;
            }],
            ['db' => 'income', 'dt' => 6, 'formatter' => function ($d) {
                return round($d, 2);
                //return $d;
            }],
            ['db' => 'redirects', 'where' => 'redirects', 'dt' => 7],
            ['db' => 'payout', 'dt' => 8],
            ['db' => 'revenue', 'dt' => 9],
            ['db' => 'profit', 'dt' => 10],
        ];

        $where = $dataTablesService::filter($request, $columns);
        $order = $dataTablesService::columnOrder($columns);
        $start = $request->get('start');
        $length = $request->get('length');

        $database = config('database.connections.mysql.database');
        $databaseAudit = config('database.connections.mysql_audit.database');

        $user = Auth()->user();
        $isAdmin = $user->hasRole('admin');

        $filterUser = '';

        if (!$isAdmin) {
            $prefix = ' AND ';
            if ($where == '') {
                $prefix = "WHERE  ";
            }

            $filterUser = $prefix . "u.id =" . $user->id;
        }

        $reapplies = DB::select(DB::raw("SELECT SQL_CALC_FOUND_ROWS u.name                                                                    AS name,
                                                                          COUNT(r.id)                                                               AS total_leads,
                                                                          SUM(IF(d.decision_status = 'sold', 1, 0))                                 AS sold_leads,
                                                                          SUM(IF(d.redirected = 1, 1, 0))                                           AS redirects,
                                                                          visitors.clicks                                                           AS clicks,
                                                                          SUM(d.decision_price)                                                     AS income,
                                                                          IF(SUM(visitors.clicks) != 0, SUM(d.decision_price) / visitors.clicks, 0) AS epc,
                                                                          IF(COUNT(r.id) != 0, SUM(d.decision_price) / COUNT(r.id), 0)              AS epl

                                                FROM $databaseAudit.reapplies AS r
                                                         LEFT JOIN $database.users AS u ON u.id = r.referral_id
                                                         LEFT JOIN $databaseAudit.decisions AS d ON d.reapply_id = r.id
                                                         LEFT JOIN (SELECT SUM(visits_amount) AS clicks,
                                                                           referral_id        AS refferal_id
                                                                    FROM $database.visitors
                                                                    GROUP BY referral_id) AS visitors ON r.referral_id = visitors.refferal_id
                                                $where $filterUser
                                                GROUP BY r.referral_id
                                                $order
                                                LIMIT $start, $length"));

        $filtered = DB::selectOne(DB::raw("SELECT FOUND_ROWS() as quantity"));
        $total = DB::selectOne(DB::raw("SELECT COUNT(*) as quantity
                                           FROM (SELECT COUNT(*) FROM $databaseAudit.reapplies GROUP BY referral_id) AS total"));

        $array = [];
        foreach ($reapplies as $item) {
            $array[] = [
                'name' => $item->name,
                'total_leads' => $item->total_leads,
                'sold_leads' => $item->sold_leads,
                'redirects' => $item->redirects,
                'clicks' => $item->clicks,
                'income' => $item->income,
                'epc' => $item->epc,
                'epl' => $item->epl,
                'revenue' => 0,
                'profit' => 0,
                'payout' => 0,
            ];
        }

        $returnData = [
            "draw" => $request->post('draw') !== null ? $request->post('draw') : 0,
            "recordsTotal" => intval($total->quantity),
            "recordsFiltered" => intval($filtered->quantity),
            "data" => $dataTablesService::prepareData($array, $columns)
        ];

        return response()->json($returnData);
    }
}
