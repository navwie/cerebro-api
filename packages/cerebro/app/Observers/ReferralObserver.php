<?php

namespace App\Observers;

use App\Models\Referral;
use App\Models\ReferralAudit;

class ReferralObserver
{
    /**
     * @param Referral $referral
     * @return void
     */
    public function created(Referral $referral)
    {
        $this->createReferralAudit($referral);
    }

    /**
     * @param Referral $referral
     * @return void
     */
    public function updated(Referral $referral)
    {
        $this->createReferralAudit($referral);
    }

    /**
     * @param Referral $referral
     * @return void
     */
    private function createReferralAudit(Referral $referral)
    {
        $data = $referral->toArray();
        $data['log_id'] = $referral->audits()->latest()->first()->id;
        ReferralAudit::create($data);
    }
}
