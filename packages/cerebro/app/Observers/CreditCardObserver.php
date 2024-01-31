<?php

namespace App\Observers;

use app\Models\CreditCard;
use App\Models\CreditCardAudit;

class CreditCardObserver
{
    /**
     * @param CreditCard $creditCard
     * @return void
     */
    public function created(CreditCard $creditCard)
    {
        $this->createCreditCardAudit($creditCard);
    }

    /**
     * @param CreditCard $creditCard
     * @return void
     */
    public function updated(CreditCard $creditCard)
    {
        $this->createCreditCardAudit($creditCard);
    }

    /**
     * @param CreditCard $bank
     * @return void
     */
    private function createCreditCardAudit(CreditCard $creditCard)
    {
        $data = $creditCard->toArray();
        $data['log_id'] = $creditCard->audits()->latest()->first()->id;
        CreditCardAudit::create($data);
    }
}
