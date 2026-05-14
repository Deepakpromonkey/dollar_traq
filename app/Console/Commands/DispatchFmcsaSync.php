<?php

namespace App\Console\Commands;

use App\Jobs\ProcessFmcsaBatchJob;
use App\Models\CarriersModel\Carrier;
use Illuminate\Console\Command;

class DispatchFmcsaSync extends Command
{
    protected $signature = 'fmcsa:sync';

    protected $description = 'Dispatch pending FMCSA carrier sync batches';

    public function handle()
    {
        $this->info('Starting FMCSA sync dispatch...');

        $total = 0;

        Carrier::query()

            ->leftJoin(
                'carrier_sync_states',
                'carriers.id',
                '=',
                'carrier_sync_states.carrier_id'
            )

            ->where(function ($q) {

                $q->whereNull('carrier_sync_states.id')

                    ->orWhereNull(
                        'carrier_sync_states.next_sync_at'
                    )

                    ->orWhere(
                        'carrier_sync_states.next_sync_at',
                        '<=',
                        now()
                    );
            })

            ->where(function ($q) {

                $q->whereNull(
                    'carrier_sync_states.is_syncing'
                )
                    ->orWhere(
                        'carrier_sync_states.is_syncing',
                        false
                    );
            })

            ->select('carriers.id')

            ->orderBy('carriers.id')

            ->chunk(500, function ($carriers) use (&$total) {

                $ids = $carriers
                    ->pluck('id')
                    ->toArray();

                ProcessFmcsaBatchJob::dispatch($ids);

                $total++;

                $this->info(
                    "Batch {$total} dispatched (".
                    count($ids).
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
