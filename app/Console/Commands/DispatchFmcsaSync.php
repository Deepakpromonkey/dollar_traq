<?php

namespace App\Console\Commands;

use App\Jobs\ProcessFmcsaBatchJob;
use App\Models\CarriersModel\Carrier;
use Illuminate\Console\Command;

class DispatchFmcsaSync extends Command
{
    protected $signature = 'fmcsa:sync';

    protected $description = 'Dispatch all FMCSA carrier sync batches';

    public function handle()
    {
        $this->info('Starting FMCSA batch dispatch...');

        $total = 0;

        Carrier::select('id')
            ->orderBy('id')
            ->chunk(20000, function ($carriers) use (&$total) {

                ProcessFmcsaBatchJob::dispatch(
                    $carriers->pluck('id')->toArray()
                );

                $total++;

                $this->info(
                    "Batch {$total} dispatched (".
                    count($carriers).
                    ' carriers)'
                );
            });

        $this->newLine();

        $this->info('================================');
        $this->info("TOTAL BATCHES: {$total}");
        $this->info('FMCSA DISPATCH COMPLETED');
        $this->info('================================');

        return Command::SUCCESS;
    }
}
