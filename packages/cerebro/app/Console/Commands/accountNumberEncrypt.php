<?php

namespace App\Console\Commands;

use App\Models\CustomerToBank;
use Illuminate\Console\Command;

class accountNumberEncrypt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accountNumberEncrypt:encrypt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command encrypt all account numbers of customers in db';

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
        while(count($customers_to_banks = CustomerToBank::limit(1000)->offset($i*1000)->get())){
            foreach($customers_to_banks as $customer_to_bank){
                $customer_to_bank->account_number = openssl_encrypt($customer_to_bank->account_number,'aes-128-cbc',$hashing_key,0,$hashing_iv);
                $customer_to_bank->saveQuietly();
            }
            $i++;
        }
        return Command::SUCCESS;
    }
}
