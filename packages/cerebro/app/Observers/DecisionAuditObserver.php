<?php

namespace app\Observers;

use App\Models\DecisionAudit;
use Illuminate\Support\Facades\Hash;

class DecisionAuditObserver
{
    /**
     * @param DecisionAudit $decisionAudit
     * @return void
     */
    public function creating(DecisionAudit $decisionAudit)
    {
        $decisionAudit->hash_id = Hash::make($decisionAudit->id);
    }
}
