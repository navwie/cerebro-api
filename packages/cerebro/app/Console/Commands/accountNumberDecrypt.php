<?php

namespace App\Console\Commands;

use App\Models\CustomerToBank;
use Illuminate\Console\Command;

class accountNumberDecrypt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accountNumberDecrypt:decrypt {from} {to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will decrypt the records for the given range of ids';

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
        $from = $this->argument('from');
        $to = $this->argument('to');
        while(count($customers_to_banks = CustomerToBank::where([
            ['id','>=',$from],
            ['id','<=',$to],
        ])->limit(1000)->offset($i*1000)->get())){
            foreach($customers_to_banks as $customer_to_bank){
                $customer_to_bank->account_number = openssl_decrypt($customer_to_bank->account_number,'aes-128-cbc',$hashing_key,0,$hashing_iv);
                $customer_to_bank->saveQuietly();
            }
            $i++;
        }
        return Command::SUCCESS;
    }
}
