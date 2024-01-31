<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardStatisticService
{
    /**
     *
     * @param Carbon $dateFrom
     * @param Carbon $dateTo
     * @param string|null $leadType
     * @param string|null $formId
     * @return array
     */
    public static function chart(Carbon $dateFrom, Carbon $dateTo, ?string $leadType, ?string $actionType, ?string $formId, ?int $flowId): array
    {
        // <editor-fold defaultstate="collapsed" desc="Preparation">
        $user = Auth()->user();
        $isAdmin = $user->hasRole('admin');
        $database = config('database.connections.mysql.database');
        $databaseAudit = config('database.connections.mysql_audit.database');
        $form = '';
        $formVisits = '';
        $flow = '';
        $flowVisits = '';
        $themeJoin = '';
        $themeJoinVisits = '';
        $userJoin = '';
        $userJoinVisits = '';
        $theme = '';
        $leadTypeQuery = '';
        $actionTypeQuery = '';
        $filterUser = '';

        if (!$isAdmin) {
            $filterUser = " AND u.id =" . $user->id;
            $userJoinVisits = "LEFT JOIN $database.users AS u ON u.id = v.referral_id";
            $userJoin = "LEFT JOIN $database.users AS u ON u.id = r.referral_id";
        }

        if (is_numeric($formId) && $formId != 0) {
            $formVisits = " AND v.referral_id =" . $formId;
            $form = " AND r.referral_id =" . $formId;
        }

        if (is_numeric($flowId) && $flowId != 0) {
            $flowVisits = " AND v.flow_id =" . $flowId;
            $flow = " AND r.flow_id =" . $flowId;
        }

        if (!is_numeric($formId) && $formId != 0) {
            $themeJoin = "LEFT JOIN $database.sites AS s ON s.form_id = r.referral_id";
            $themeJoinVisits = "LEFT JOIN $database.sites AS s ON s.form_id = v.referral_id";
            $theme = " AND s.theme ='" . $formId . "'";
        }

        if ($leadType != 'all') {
            $leadTypeQuery = " AND r.lead_type = '$leadType'";
        }

        if ($actionType != 'all') {
            $actionTypeQuery = " AND r.action_type = '$actionType'";
        }

        $oneDay = false;
        if ($dateFrom->equalTo($dateTo)) {
            $sqlPart = 'HOUR';
            $oneDay = true;
        } elseif ($dateFrom->diffInMonths($dateTo) >= 1) {
            $sqlPart = "MONTH";

        } else {
            $sqlPart = "DATE";
        }

        $from = $dateFrom->format('Y-m-d 00:00:00');
        $to = $dateTo->format('Y-m-d 23:59:59');

        // </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="VisitorsToSubmits">

        $visitorsSql = DB::raw("  SELECT  $sqlPart(v.created_at)      AS 'labels',
                                                COUNT(v.id)                   AS 'visits'
                                        FROM $database.visitors AS v
                                        $userJoinVisits
                                        $themeJoinVisits
                                        WHERE v.created_at BETWEEN '$from' AND '$to' $filterUser $formVisits $flowVisits $theme
                                        GROUP BY $sqlPart(v.created_at)
                                        ORDER BY $sqlPart(v.created_at)");

        $visitors = DB::select($visitorsSql);

        $vis = [];
        foreach ($visitors as $visitor) {
            $vis[$visitor->labels] = $visitor->visits;
        }

        $sub = [];
        $submitsSql = DB::raw("   SELECT  $sqlPart(r.created_at)   AS 'labels',
                                                COUNT(r.id)              AS 'submits'
                                        FROM $databaseAudit.reapplies AS r
                                        LEFT JOIN $databaseAudit.decisions AS d ON r.id = d.reapply_id
                                        $userJoin
                                        $themeJoin
                                        WHERE d.decision_status != 'test' AND r.created_at BETWEEN '$from' AND '$to' $filterUser $form $flow $theme $leadTypeQuery $actionTypeQuery
                                        GROUP BY $sqlPart(r.created_at)
                                        ORDER BY $sqlPart(r.created_at)");

        $submits = DB::select($submitsSql);

        foreach ($submits as $submit) {
            $sub[$submit->labels] = $submit->submits;
        }
        $result = [];
        $start = $oneDay ? 0 : (count($vis) > 0 ? min(array_keys($vis)) : 0);
        $stop = $oneDay ? 23 : (count($vis) > 0 ? max(array_keys($vis)) : 0);

        for ($i = $start; $i <= $stop; $i++) {
            $visit = $vis[$i] ?? 0;
            $submit = $sub[$i] ?? 0;
            $result['visitors']['labels'][] = $i;
            $result['visitors']['data'][] = number_format($visit != 0 ? $submit * 100 / $visit : 0, 2);
        }
        // </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="SoldsToSubmits">
        $soldSubmitsSql = DB::raw("  SELECT   $sqlPart(r.created_at)                                                                AS 'labels',
                                                    IF(COUNT(r.id) != 0, FORMAT(
                                                           SUM(IF(d.decision_status = 'sold', 1, 0)) * 100 / COUNT(r.id), 2), 0) AS submits_solds
                                            FROM $databaseAudit.reapplies AS r
                                                    LEFT JOIN $databaseAudit.decisions AS d ON d.reapply_id = r.id
                                                    $userJoin
                                                    $themeJoin
                                            WHERE d.decision_status != 'test' AND r.created_at BETWEEN '$from' AND '$to' $filterUser $form $flow $theme $leadTypeQuery $actionTypeQuery
                                            GROUP BY $sqlPart(r.created_at)
                                            ORDER BY $sqlPart(r.created_at)
        ");
        $submits = DB::select($soldSubmitsSql);

        $start = $oneDay ? 0 : (count($submits) > 0 ? min(array_keys($submits)) : 0);
        $stop = $oneDay ? 23 : (count($submits) > 0 ? max(array_keys($submits)) : 0);

        for ($i = $start; $i <= $stop; $i++) {
            $result['submits']['labels'][] = $i;
            $result['submits']['data'][] = $submits[$i]->submits_solds ?? 0;
        }
        // </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="Redirect rate">
        $sql = DB::raw("  SELECT $sqlPart(r.created_at)                                    AS labels,
                                IF((SUM(IF(d.decision_status = 'sold', 1, 0))) != 0, FORMAT(
                                                SUM(IF(d.redirected = 1, 1, 0)) * 100 /
                                                SUM(IF(d.decision_status = 'sold', 1, 0)), 2),0) AS redirect_rate
                                FROM $databaseAudit.reapplies AS r
                                         LEFT JOIN $databaseAudit.decisions AS d ON d.reapply_id = r.id
                                         $userJoin
                                         $themeJoin
                                WHERE d.decision_status != 'test' AND r.created_at BETWEEN '$from' AND '$to' $filterUser $form $flow $theme $leadTypeQuery $actionTypeQuery
                                GROUP BY $sqlPart(r.created_at)
                                ORDER BY $sqlPart(r.created_at)
        ");
        $redirects = DB::select($sql);

        $start = $oneDay ? 0 : (count($redirects) > 0 ? min(array_keys($redirects)) : 0);
        $stop = $oneDay ? 23 : (count($redirects) > 0 ? max(array_keys($redirects)) : 0);

        for ($i = $start; $i <= $stop; $i++) {
            $result['redirects']['labels'][] = $i;
            $result['redirects']['data'][] = $redirects[$i]->redirect_rate ?? 0;
        }
        // </editor-fold>
        return $result;
    }

}

