<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Import;
use App\Services\MultiProcess;

class parseImportCSVNew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importCSVNew:parse {processAmount?} {onlyAdd?}';

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
        $fileName = end($newFiles);

        if(empty($fileName)) {
            $this->error("No files in folder");
            return false;
        }

        if(Import::where('status', 'in_progress')->first()) {
            $this->error("Other import in progress");
            return false;
        }

        $processAmount = $this->argument('processAmount');
        $onlyAdd = $this->argument('onlyAdd') ?? 0;
        ini_set('memory_limit', '1024M'); // dangerous behavior
        
        $this->line('Parsing - ' . $fileName); 

        $path = Storage::disk('import')->temporaryUrl($fileName, now()->addDay(1));
        $handle = file($path);
        unset($handle[0]); // remove headers
        $chunkSize = 1000;
        $chunks = array_chunk($handle, $chunkSize);
        $scripts = array();

        $import = Import::create(array(
            'file_name' => $fileName,
            'status' => 'in_progress',
            'total_count' => count($handle),
            'applyed_count' => 0,
            'errors' => '',
            'warnings' => ''            
        ));
        unset($handle);
        foreach ($chunks as $key => $chunk) {
            $scripts[] = ["php", base_path('artisan'), "MultiProcessImport:run", $fileName, $key, $chunkSize, $onlyAdd];
        }
        $multi = new MultiProcess($scripts, $processAmount, $onlyAdd);
        $import->update(array('status' => 'done', 'errors' => json_encode($multi->getErrors()), 'applyed_count' => $multi->getApplyedCount()));
        return Command::SUCCESS;
    }
}