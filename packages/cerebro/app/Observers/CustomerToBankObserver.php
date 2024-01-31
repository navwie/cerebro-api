<?php

namespace App\Observers;

use App\Models\CustomerToBank;
use App\Models\CustomerToBankAudit;

class CustomerToBankObserver
{
    /**
     * @param CustomerToBank $customerToBank
     * @return void
     */
    public function created(CustomerToBank $customerToBank)
    {
        $this->createCustomerToBankAudit($customerToBank);
    }

    /**
     * @param CustomerToBank $customerToBank
     * @return void
     */
    public function updated(CustomerToBank $customerToBank)
    {
        $this->createCustomerToBankAudit($customerToBank);
    }

    /**
     * @param CustomerToBank $customerToBank
     * @return void
     */
    private function createCustomerToBankAudit(CustomerToBank $customerToBank)
    {
        $data = $customerToBank->toArray();
        $data['log_id'] = $customerToBank->audits()->latest()->first()->id;
        CustomerToBankAudit::create($data);
    }
}
