<?php

namespace App\Console\Commands;

use App\Jobs\HarvestPrintBooksReport;
use App\Jobs\HarvestEBooksReport;
use Illuminate\Console\Command;

class HarvestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'harvest {days=60}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Harvest Analytics report data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Event::listen('illuminate.log', function($level, $msg, $extras)
        {
            $msg = $msg . ' ' . json_encode($extras);
            $this->line($msg, $level);
        });

        $days = $this->argument('days');
        \Log::info("Dispatching jobs to harvest the last {$days} days of records");

        dispatch(new HarvestPrintBooksReport($days));
        dispatch(new HarvestEBooksReport($days));
    }
}
