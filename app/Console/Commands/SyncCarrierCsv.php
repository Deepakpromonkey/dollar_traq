<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncCarrierCsv extends Command
{
    protected $signature = 'sync:carriers 
                            {file=storage/app/public/newdata.csv}';

    protected $description = 'Ultra Fast FMCSA Carrier CSV Sync';

    private int $batchSize = 200;

    public function handle()
    {
        ini_set('memory_limit', '1024M');

        gc_enable();

        DB::connection()->disableQueryLog();

        $path = base_path($this->argument('file'));

        if (! file_exists($path)) {

            $this->error("CSV not found: {$path}");

            return Command::FAILURE;
        }

        $handle = fopen($path, 'r');

        if (! $handle) {

            $this->error('Unable to open CSV');

            return Command::FAILURE;
        }

        $header = fgetcsv($handle);

        if (! $header) {

            $this->error('CSV is empty');

            fclose($handle);

            return Command::FAILURE;
        }

        $header = array_map('trim', $header);

        $this->info('CSV Loaded...');
        $this->info('Starting Ultra Fast Sync...');

        $carriers = [];

        $processed = 0;

        $failed = 0;

        $startedAt = microtime(true);

        while (($row = fgetcsv($handle)) !== false) {

            try {

                if (count($header) !== count($row)) {
                    continue;
                }

                $data = array_combine($header, $row);

                /*
                |--------------------------------------------------------------------------
                | UNIQUE KEY = DOT_NUMBER
                |--------------------------------------------------------------------------
                */

                $dotNumber = $this->number(
                    $data['DOT_NUMBER'] ?? null
                );

                if (! $dotNumber) {
                    continue;
                }

                $now = now();

                $carriers[] = [

                    /*
                    |--------------------------------------------------------------------------
                    | UNIQUE IDENTIFIER
                    |--------------------------------------------------------------------------
                    */

                    'dot_number' => $dotNumber,

                    /*
                    |--------------------------------------------------------------------------
                    | BASIC DETAILS
                    |--------------------------------------------------------------------------
                    */

                    'row_id' => (string) Str::uuid(),

                    'docket_number' => $this->number(
                        $data['DOCKET1'] ?? null
                    ),

                    'docket_prefix' => $this->string(
                        $data['DOCKET1PREFIX'] ?? null
                    ),

                    'legal_name' => $this->string(
                        $data['LEGAL_NAME'] ?? null
                    ),

                    'dba_name' => $this->string(
                        $data['DBA_NAME'] ?? null
                    ),

                    /*
                    |--------------------------------------------------------------------------
                    | CONTACT
                    |--------------------------------------------------------------------------
                    */

                    'telephone_number' => $this->phone(
                        $data['PHONE'] ?? null
                    ),

                    'fax_number' => $this->phone(
                        $data['FAX'] ?? null
                    ),

                    'cellphone_number' => $this->phone(
                        $data['CELL_PHONE'] ?? null
                    ),

                    'email_address' => $this->string(
                        $data['EMAIL_ADDRESS'] ?? null
                    ),

                    /*
                    |--------------------------------------------------------------------------
                    | COMPANY
                    |--------------------------------------------------------------------------
                    */

                    'company_contact_primary' => $this->string(
                        $data['COMPANY_OFFICER_1'] ?? null
                    ),

                    'company_contact_secondary' => $this->string(
                        $data['COMPANY_OFFICER_2'] ?? null
                    ),

                    'organization_type_desc' => $this->string(
                        $data['BUSINESS_ORG_DESC'] ?? null
                    ),

                    /*
                    |--------------------------------------------------------------------------
                    | DRIVER + VEHICLE
                    |--------------------------------------------------------------------------
                    */

                    'total_drivers' => $this->number(
                        $data['TOTAL_DRIVERS'] ?? null
                    ),

                    'total_drivers_cdl' => $this->number(
                        $data['TOTAL_CDL'] ?? null
                    ),

                    'owned_trucks' => $this->number(
                        $data['TRUCK_UNITS'] ?? null
                    ),

                    'total_buses' => $this->number(
                        $data['BUS_UNITS'] ?? null
                    ),

                    /*
                    |--------------------------------------------------------------------------
                    | MILEAGE
                    |--------------------------------------------------------------------------
                    */

                    'mcs150_mileage' => $this->number(
                        $data['MCS150_MILEAGE'] ?? null
                    ),

                    'mcs150_year' => $this->number(
                        $data['MCS150_MILEAGE_YEAR'] ?? null
                    ),

                    'mcs150_date' => $this->date(
                        $data['MCS150_DATE'] ?? null
                    ),

                    /*
                    |--------------------------------------------------------------------------
                    | STATUS
                    |--------------------------------------------------------------------------
                    */

                    'added_date' => $this->date(
                        $data['ADD_DATE'] ?? null
                    ),

                    'usdot_status' => $this->string(
                        $data['STATUS_CODE'] ?? null
                    ),

                    'safety_rating_desc' => $this->string(
                        $data['SAFETY_RATING'] ?? null
                    ),

                    'safety_rating_date' => $this->date(
                        $data['SAFETY_RATING_DATE'] ?? null
                    ),

                    /*
                    |--------------------------------------------------------------------------
                    | TIMESTAMPS
                    |--------------------------------------------------------------------------
                    */

                    'created_at' => $now,

                    'updated_at' => $now,
                ];

                $processed++;

                /*
                |--------------------------------------------------------------------------
                | BATCH UPSERT
                |--------------------------------------------------------------------------
                */

                if (count($carriers) >= $this->batchSize) {

                    $this->upsertCarriers($carriers);

                    $elapsed = round(
                        microtime(true) - $startedAt,
                        2
                    );

                    $this->info(
                        "Processed: {$processed} rows | Time: {$elapsed}s"
                    );

                    $carriers = [];

                    unset($row, $data);

                    gc_collect_cycles();
                }

            } catch (\Throwable $e) {

                $failed++;

                $this->error(
                    'FAILED => '.
                    ($data['DOT_NUMBER'] ?? 'UNKNOWN').
                    ' => '.
                    $e->getMessage()
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FINAL REMAINING BATCH
        |--------------------------------------------------------------------------
        */

        if (! empty($carriers)) {

            $this->upsertCarriers($carriers);

            unset($carriers);

            gc_collect_cycles();
        }

        fclose($handle);

        $totalTime = round(
            microtime(true) - $startedAt,
            2
        );

        $this->newLine();

        $this->info('======================================');
        $this->info("TOTAL ROWS PROCESSED: {$processed}");
        $this->info("FAILED ROWS: {$failed}");
        $this->info("TOTAL TIME: {$totalTime} seconds");
        $this->info('======================================');

        return Command::SUCCESS;
    }

    /*
    |--------------------------------------------------------------------------
    | UPSERT
    |--------------------------------------------------------------------------
    |
    | IF DOT EXISTS => UPDATE
    | IF DOT NOT EXISTS => INSERT
    |
    |--------------------------------------------------------------------------
    */

    private function upsertCarriers(array $carriers): void
    {
        DB::table('carriers')->upsert(

            $carriers,

            /*
            |--------------------------------------------------------------------------
            | UNIQUE COLUMN
            |--------------------------------------------------------------------------
            */

            ['dot_number'],

            /*
            |--------------------------------------------------------------------------
            | UPDATE THESE COLUMNS IF DOT EXISTS
            |--------------------------------------------------------------------------
            */

            [
                'docket_number',

                'docket_prefix',

                'legal_name',

                'dba_name',

                'telephone_number',

                'fax_number',

                'cellphone_number',

                'email_address',

                'company_contact_primary',

                'company_contact_secondary',

                'organization_type_desc',

                'total_drivers',

                'total_drivers_cdl',

                'owned_trucks',

                'total_buses',

                'mcs150_mileage',

                'mcs150_year',

                'mcs150_date',

                'added_date',

                'usdot_status',

                'safety_rating_desc',

                'safety_rating_date',

                'updated_at',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    private function string($value): ?string
    {
        $value = trim((string) $value);

        return $value !== ''
            ? $value
            : null;
    }

    private function number($value): ?string
    {
        if ($value === '' || $value === null) {
            return null;
        }

        $value = preg_replace('/[^0-9]/', '', (string) $value);

        return $value !== ''
            ? $value
            : null;
    }

    private function phone($value): ?string
    {
        return $this->number($value);
    }

    private function date($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $timestamp = strtotime($value);

        return $timestamp
            ? date('Y-m-d', $timestamp)
            : null;
    }
}
