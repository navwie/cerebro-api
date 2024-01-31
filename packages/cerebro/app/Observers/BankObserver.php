<?php

namespace App\Observers;
use App\Models\Bank;
use App\Models\BankAudit;

class BankObserver
{
    /**
     * @param Bank $bank
     * @return void
     */
    public function created(Bank $bank)
    {
        $this->createBankAudit($bank);
    }

    /**
     * @param Bank $bank
     * @return void
     */
    public function updated(Bank $bank)
    {
        $this->createBankAudit($bank);
    }

    /**
     * @param Bank $bank
     * @return void
     */
    private function createBankAudit(Bank $bank)
    {
        $data = $bank->toArray();
        $data['log_id'] = $bank->audits()->latest()->first()->id;
        BankAudit::create($data);
    }

}
