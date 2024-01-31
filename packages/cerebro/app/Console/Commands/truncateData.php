<?php

namespace app\Console\Commands;

use App\Models\LogDnm;
use App\Models\LogPixel;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class truncateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'truncateData:run';

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
        LogDnm::where('created_at', '<', Carbon::now()->subDays(config('dnm.truncateDays'))->format('Y-m-d 00:00:00'))->delete();
        LogPixel::where('created_at', '<', Carbon::now()->subDays(config('dnm.truncateDays'))->format('Y-m-d 00:00:00'))->delete();

        //TODO it seems like sessions table clears automatically.
        //DB::raw('DELETE FROM sessions WHERE `user_id` IS NULL AND `user_agent` LIKE "Wget%"');

        return Command::SUCCESS;
    }
}
