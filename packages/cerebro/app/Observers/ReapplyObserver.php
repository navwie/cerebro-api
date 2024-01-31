<?php

namespace App\Observers;

use App\Models\Reapply;
use App\Models\ReapplyAudit;
use Illuminate\Support\Facades\Log;

class ReapplyObserver
{
    /**
     * @param Reapply $reapply
     * @return void
     */
    public function created(Reapply $reapply)
    {
        $this->createReapplyAudit($reapply);
    }

    /**
     * @param Reapply $reapply
     * @return void
     */
    public function updated(Reapply $reapply)
    {
        $this->createReapplyAudit($reapply);
    }

    /**
     * @param Reapply $reapply
     * @return void
     */
    private function createReapplyAudit(Reapply $reapply)
    {
        $data = $reapply->toArray();
        $data['log_id'] = $reapply->audits()->latest()->first()->id;
        ReapplyAudit::create($data);
    }
}
