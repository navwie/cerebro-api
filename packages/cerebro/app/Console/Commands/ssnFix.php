<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;

class ssnFix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ssnFix:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command fix all ssn of customers in db';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $i = 0;
        while(count($customers = Customer::limit(1000)->offset($i*1000)->get())){
            foreach($customers as $customer){
                if(strlen($customer->ssn) < 9){
                    $customer->ssn = str_pad($customer->ssn,9,'0',STR_PAD_LEFT);
                    $customer->ssn_short = substr($customer->ssn,-4);
                    $customer->saveQuietly();
                }
            }
            $i++;
        }
        return Command::SUCCESS;
    }
}
