<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;

class ssnShortFill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ssnShortFill:fill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command fill all ssn_short of customers in db';

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
                $customer->ssn_short = substr($customer->ssn,-4);
                $customer->saveQuietly();
            }
            $i++;
        }
        return Command::SUCCESS;
    }
}
