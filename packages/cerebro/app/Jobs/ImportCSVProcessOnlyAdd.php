<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Reapply;
use App\Models\Customer;
use App\Models\Bank;
use App\Models\CustomerToBank;
use App\Models\Decision;
use App\Models\User;
use Exception;

class ImportCSVProcessOnlyAdd implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public $header;
    public $errors;
    public $applyedCount;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $header)
    {
        $this->data   = $data;
        $this->header = $header;
        $this->errors = array();
        $this->applyedCount = 0;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $defaulPassword = bcrypt(config('dnm.defaultFormSettings.password'));
        $defaultPostBackAmount = config('dnm.defaultFormSettings.postBackAmount');
        $defaultPersonalMinReq = config('dnm.defaultFormSettings.personalMinReq');
        $typePersonal = User::TYPE_SOURCE_PERSONAL;
        $typeLead = User::TYPE_SOURCE_LEAD;
        $dataProcessed = array();
        $emails = array();

        foreach ($this->data as $row) {
            try {
                $dataProcessed[] = $data = array_combine($this->header, $row);                
                $emails[] = $data['customer_email'];
            } catch(Exception $e) {
                $this->errors[] = $e->getMessage();
            }
        }
        $emails = array_map('strtolower', $emails);
        $existCustomers = Customer::select('email')->whereIn('email', $emails)->get()->toArray();
        foreach ($dataProcessed as $data) {
            try {
                $chanelData = array();
                $newFormData = [
                    'name'=>$data[key($data)], // TODO: figureout why it's working only this way insteed of just a key "source_name"
                    'menuroles'=>'user', 'password'=>$defaulPassword,
                    'post_back_amount'=>$defaultPostBackAmount, 'personal_min_req'=>$defaultPersonalMinReq,
                ];
                if($data['source_type'] == $typePersonal)
                {
                    $chanelData['personal_channel_id'] = $data['channel_id'];
                    $chanelData['personal_password'] = $data['password'];
                }
                elseif($data['source_type'] == $typeLead)
                {
                    $chanelData['lead_channel_id'] = $data['channel_id'];
                    $chanelData['lead_password'] = $data['password'];
                }
                $newFormData = array_merge($newFormData, $chanelData);
                $form = User::firstOrNew(['email'=>$data['source_email']], $newFormData);
                if($form->exists == false)
                {
                    $form->save();
                }
                else
                {
                    $form->update($chanelData);
                }

                if(in_array(strtolower($data['customer_email']),  array_map('strtolower', array_column($existCustomers, 'email'))))
                {
                    continue;
                } 

                $data['ssn_short'] = openssl_encrypt(substr($data['ssn'], -4), 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv'));
                $data['ssn'] = openssl_encrypt($data['ssn'], 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv'));
                $data['account_number'] = openssl_encrypt($data['account_number'], 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv'));
                
                $customer = Customer::firstOrNew(['email'=>$data['customer_email']], $data);
                if($customer->exists == false)
                {
                    $customer->saveQuietly();
                }

                $bank = Bank::firstOrNew(['routing_number'=>$data['routing_number']], $data);
                if($bank->exists == false)
                {
                    $bank->saveQuietly();
                }
                else
                {
                    $bank->updateQuietly($data);
                }
                $data['bank_id'] = $bank['id'];
                $data['customer_id'] = $customer['id'];
                $data['referral_id'] = $form->id;

                $ctb = CustomerToBank::firstOrNew(['customer_id' => $customer->id], $data);
                if($ctb->exists == false)
                {
                    $ctb->saveQuietly();
                }
                $reapply = Reapply::firstOrNew(['customer_id' => $customer->id], $data);
                if($reapply->exists == false)
                {
                    $reapply->saveQuietly();
                }
                $data['reapply_id'] = $reapply['id'];

                $decision = Decision::firstOrNew([
                    'customer_id' => $customer->id
                ], $data);
                if($decision->exists == false)
                {
                    $decision->saveQuietly();
                }
                $this->applyedCount++;
            } catch(Exception $e) {
                $this->errors[] = $e->getMessage();
            }
        }
    }
}
