<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reapply;
use App\Models\Customer;
use Illuminate\Support\Facades\Storage;
use App\Models\Bank;
use App\Models\CustomerToBank;
use App\Models\ReapplyAudit;
use App\Models\CustomerAudit;
use App\Models\BankAudit;
use App\Models\CustomerToBankAudit;
use App\Models\Import;
use App\Models\Decision;
use App\Models\User;
use Exception;

class parseImportCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importCSV:parse {fileName?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command parse imported file and fill DB with reapply data';

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
        $fileName = $this->argument('fileName');
        $errors = array();
        $manuallyFileImport = isset($fileName);
        $ifExist = $manuallyFileImport ?? Storage::disk('import')->exists($fileName);
        if($manuallyFileImport && !$ifExist) {
            $this->error("File doesn't exist");
            return false;
        }

        if(!$manuallyFileImport) {
            $files = Storage::disk('import')->allFiles();
            
            if(empty($files)) {
                $this->error("No files in folder");
                return false;
            }

            $fileNames = array_map(function($file){
                return basename($file);
            }, $files);
            $importedFiles = Import::pluck('file_name')->toArray();
            $newFiles = array_diff($fileNames, $importedFiles);
            $fileName = array_shift($newFiles);

            if(empty($fileName)) {
                $this->error("No files in folder");
                return false;
            }
        }

        $this->newLine();
        
        $this->line('Parsing - ' . $fileName);
        
        $path = Storage::disk('import')->path($fileName);

        $handle = fopen($path, 'r');

        $header = array();
        $row = fgetcsv($handle);
        $header = array_map('trim', $row);
        $processedAmount = 0;
        while ($row = fgetcsv($handle))
        {
            $processedAmount++;
        }
        fclose($handle);

        $import = Import::create(array(
            'file_name' => $fileName,
            'status' => 'in_progress',
            'total_count' => $processedAmount,
            'applyed_count' => 0,
            'errors' => '',
            'warnings' => ''            
        ));


        $bar = $this->output->createProgressBar($processedAmount);
        $bar->start();

        $index = 1;
        $applyedCount = 0;
        $defaulPassword = bcrypt(config('dnm.defaultFormSettings.password'));
        $defaultPostBackAmount = config('dnm.defaultFormSettings.postBackAmount');
        $defaultPersonalMinReq = config('dnm.defaultFormSettings.personalMinReq');
        $typePersonal = User::TYPE_SOURCE_PERSONAL;
        $typeLead = User::TYPE_SOURCE_LEAD;

        $handle = fopen($path, 'r');
        while ($row = fgetcsv($handle))
        {
            if ($index == 1) //Skip Header
            {
                $index++;
                continue;
            }
            $data = array_combine($header, $row);
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

                $data['ssn_short'] = openssl_encrypt(substr($data['ssn'], -4), 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv'));
                $data['ssn'] = openssl_encrypt($data['ssn'], 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv'));
                $data['account_number'] = openssl_encrypt($data['account_number'], 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv'));
               
                $customer = Customer::firstOrNew(['email'=>$data['customer_email']], $data);
                if($customer->exists == false)
                {
                    $customer->saveQuietly();
                }
                else
                {
                    $customer->updateQuietly($data);
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
                else
                {
                    $ctb->updateQuietly($data);
                }

                $reapply = Reapply::firstOrNew(['customer_id' => $customer->id], $data);
                if($reapply->exists == false)
                {
                    $reapply->saveQuietly();
                }
                else
                {
                    $reapply->updateQuietly($data);
                }
                $data['reapply_id'] = $reapply['id'];

                $decision = Decision::firstOrNew([
                    'customer_id' => $customer->id
                ], $data);
                if($decision->exists == false)
                {
                    $decision->saveQuietly();
                }
                else
                {
                    $decision->updateQuietly($data);
                }
                $applyedCount++;
            } catch (Exception $e) {
                array_push($errors, $e->getMessage());
            }
            $bar->advance();
        }
        fclose($handle);
        $bar->finish();
        $import->update(array('status' => 'done', 'errors' => json_encode($errors), 'applyed_count' => $applyedCount));
        return Command::SUCCESS;
    }
}
