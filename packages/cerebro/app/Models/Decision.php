<?php

namespace App\Models;

use App\Models\Traits\CanSaveQuietly;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Contracts\Auditable;

class Decision extends Model implements Auditable
{
    use HasFactory, CanSaveQuietly;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'reapply_id',
        'referral_id',
        'decision_id',
        'decision_status',
        'decision_message',
        'decision_price',
        'decision_redirect',
        'redirected',
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
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function customer()
    {
        return $this->hasOne('App\Models\Customer', 'id', 'customer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function reapply()
    {
        return $this->hasOne('App\Models\Reapply', 'id', 'reapply_id');
    }

    public static function saveDecision($reapply, $formData, $userId, &$response)
    {
        $decisionData = [
            'reapply_id' => $reapply->id,
            'decision_id' => isset($response['id']) ? $response['id'] : 0,
            'referral_id' => $userId,
            'decision_status' => isset($response['status']) ? $response['status'] : '',
            'decision_message' => isset($response['message']) ? (is_array($response['message']) ? '' : $response['message']) : '',
            'decision_price' => isset($response['price']) ? ($response['status'] !== 'test' && $response['status'] !== 'sold' && !is_numeric($response['price'])) ? 0 : $response['price'] : 0,
            'decision_redirect' => isset($response['redirect']) ? $response['redirect'] : '',
            'redirected' => 0
        ];

        try {
            $decision = Decision::updateOrCreate([
                'customer_id' => $reapply->customer->id
            ], $decisionData);

            LogPixel::where([
                'customer_id' => $decision->customer_id,
                'reapply_id' => $decision->reapply_id,
            ])
                ->latest()
                ->first()
                ->update([
                'decision_id' => $decision->id,
            ]);
            $decisionData = $decision->toArray();
            $decisionData['reapply_id'] = $formData['reapply_audit_id'];
            $decisionData['customer_id'] = $formData['customer_audit_id'];
            $decisionData['log_id'] = (Decision::find($decisionData['id']))->audits()->latest()->first()->id;
            $decisionAudit = DecisionAudit::create($decisionData);
            $decision->decision_autdit_id = $decisionAudit->id;
            return $decision;
        } catch (\Exception $e) {
            if(config('logging.channels.decisions.turn')){
                Log::channel('decisions')->notice('line ' . $e->getLine() . ' :' . $e->getMessage());
            }
            $response['status'] = 'error';
            $response['message'] = $e->getMessage();
            return (object)[
                'id' => 0,
            ];
        }
    }

    public static function markRedirected($decisionAuditId, $requestIdMark)
    {
        $decisionAudit = DecisionAudit::where('hash_id', $decisionAuditId)->first();
        if ($decisionAudit) {
            $decisionAudit->update([
                'redirected' => 1
            ]);
            $reapplyAudit = ReapplyAudit::find($decisionAudit->reapply_id);
            $reapplyAudit->update([
                'request_id_mark' => $requestIdMark
            ]);
            $decision = Decision::where('customer_id', $decisionAudit->customer_id)->first();
            if ($decision) {
                $decision->redirected = 1;
                $decision->saveQuietly();
            } else {
                Log::channel('decisions')->error('DecisionAuditID: ' . $decisionAuditId . '| Decision audit : ' . json_encode($decisionAudit->toArray()));
            }
        } else {
            Log::channel('decisions')->error('DecisionAuditID: ' . $decisionAuditId);
        }
    }
}
