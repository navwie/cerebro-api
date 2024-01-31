<?php

namespace App\Providers;

use App\Models\Bank;
use App\Models\CreditCard;
use App\Models\CustomerToBank;
use App\Models\DecisionAudit;
use App\Models\Referral;
use App\Observers\BankObserver;
use App\Observers\CreditCardObserver;
use App\Observers\CustomerToBankObserver;
use App\Observers\DecisionAuditObserver;
use App\Observers\ReferralObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        //Customer::observe(CustomerObserver::class);
        Bank::observe(BankObserver::class);
        Referral::observe(ReferralObserver::class);
        DecisionAudit::observe(DecisionAuditObserver::class);
        //Reapply::observe(ReapplyObserver::class);
        CustomerToBank::observe(CustomerToBankObserver::class);
        CreditCard::observe(CreditCardObserver::class);
    }
}
