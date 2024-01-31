<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;

class ssnEncrypt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ssnEncrypt:encrypt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command encrypt all ssn of customers in db';

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
        $hashing_key = config('hashing.hashing_key');
        $hashing_iv = config('hashing.hashing_iv');
        while(count($customers = Customer::limit(1000)->offset($i*1000)->get())){
            foreach($customers as $customer){
                $customer->ssn = openssl_encrypt($customer->ssn,'aes-128-cbc',$hashing_key,0,$hashing_iv);
                $customer->ssn_short = openssl_encrypt($customer->ssn_short,'aes-128-cbc',$hashing_key,0,$hashing_iv);
                $customer->saveQuietly();
            }
            $i++;
        }
        return Command::SUCCESS;
    }
}
