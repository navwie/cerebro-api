<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\CustomerAudit;

class CustomerObserver
{
    /**
     * @param Customer $customer
     * @return void
     */
    public function created(Customer $customer)
    {
        $this->createCustomerAudit($customer);
    }

    /**
     * @param Customer $customer
     * @return void
     */
    public function updated(Customer $customer)
    {
        $this->createCustomerAudit($customer);
    }

    /**
     * @param Customer $customer
     * @return void
     */
    private function createCustomerAudit(Customer $customer)
    {
        $data = $customer->toArray();
        $data['log_id'] = $customer->audits()->latest()->first()->id;
        CustomerAudit::create($data);
    }
}
