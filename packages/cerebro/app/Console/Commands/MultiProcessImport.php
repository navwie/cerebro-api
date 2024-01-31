<?php

namespace App\Console\Commands;

use App\Jobs\ImportCSVProcess;
use App\Jobs\ImportCSVProcessOnlyAdd;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MultiProcessImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MultiProcessImport:run {fileName} {chunkId} {chunkSize} {onlyAdd}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run multiple import process';

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
        $chunkid = $this->argument('chunkId');
        $chunkSize = $this->argument('chunkSize');
        $onlyAdd = $this->argument('onlyAdd');
        $path = Storage::disk('import')->temporaryUrl($fileName, now()->addDay(1));
        ini_set('memory_limit', '1024M');
        $file = file($path);
        $header = str_getcsv($file[0]);
        unset($file[0]);;
        $chunks = array_chunk($file, $chunkSize);
        $chunk = $chunks[$chunkid];
        unset($chunks);
        unset($file);
        
        $csvData = array_map('str_getcsv', $chunk);
        if($onlyAdd) {
            $import = new ImportCSVProcessOnlyAdd($csvData, $header);
        } else {            
            $import = new ImportCSVProcess($csvData, $header);
        }
        $import->handle();
        echo json_encode(array("errors" => $import->errors, "applyedCount" => $import->applyedCount));
    }
}
