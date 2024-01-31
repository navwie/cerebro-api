<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Reapply;
use Illuminate\Console\Command;

class postBackRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendPBR {fileName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $fileName = $this->argument('fileName');
        $handle = fopen($fileName,'r');

        $header = array();
        $row = fgetcsv($handle);
        $header = array_map('trim', $row);
        $index = 1;

        if($handle){
            while ($row = fgetcsv($handle)) {
                $data = array_combine($header, $row);
                $stingData = implode( ', ',  $row);
                $url = 'https://postback.dnmstats.net/?clickid=[clickId]&amount=[amount]&currency=USD&transaction_id=[transactionId]&key=4f6b76fef28309392e3fb0d35a13eb0e5a705f7f';
                if ($this->confirm("Data from row below will be send:\n $stingData\n")) {
                    $customer = Customer::where(['email' => $data['email']])
//                        ->where('first_name', $data['first_name'])
//                        ->where('last_name', $data['last_name'])
                    ->first();
                    if($customer == null){
                        $this->info("Customer was not found.\nCan't send request.");
                    }else{
                        $reapply = Reapply::where('customer_id',$customer->id)->first();
                        if($reapply == null){
                            $this->info("Reapply was not found.\nCan't send request.");
                        }else{
                            $transactionId = hash('sha256',$data['email'] . $data['date']);
                            //$transactionId = hash('sha256',$data['first_name'] . $data['last_name'] . $data['date']);
                            $search = ['[clickId]','[amount]','[transactionId]'];
                            $replace = [$reapply->click_id,$data['price'],$transactionId];
                            $url = str_replace($search,$replace,$url);
                            $sub_ids = json_decode($reapply->sub_ids,true);
                            if($sub_ids){
                                if($data['sub_id'] !== $sub_ids['subId']){
                                    $this->info('Sub ids not equal');
                                    $this->info("Sub id in file: ".$data['sub_id']);
                                    $this->info("Sub id in db: ".$sub_ids['subId']);
                                    continue;
                                }
                            }else{
                                $this->info('Error parse sub ids from db, sub_id:'.$reapply->sub_id);
                                continue;
                            }

                            if ($this->confirm("Send request to url $url?")) {
                                $reapply->transaction_id = $transactionId;
                                $reapply->saveQuietly();
                                $ch = curl_init();
                                if (!$ch) {
                                    die("Couldn't initialize a cURL handle");
                                }
                                curl_setopt($ch, CURLOPT_URL, $url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                $response = curl_exec($ch);
                                $this->info($response);
                                curl_close($ch);
                            }else{
                                $this->info("Row was skipped.");
                            }
                        }
                    }
                }else{
                    $this->info(" Row was skipped.");
                }
            }
            fclose($handle);
        }else{
            $this->info('File cant be opened!');
            return Command::INVALID;
        }
        return Command::SUCCESS;
    }
}
