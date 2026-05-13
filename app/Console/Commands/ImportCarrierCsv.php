<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCarrierCsv extends Command
{
    protected $signature = 'import:carriers';

    protected $description = 'Ultra Fast Carrier CSV Import';

    private int $batchSize = 200;

    public function handle()
    {
        ini_set('memory_limit', '1024M');

        gc_enable();

        ini_set('auto_detect_line_endings', true);

        $path = storage_path('app/public/carriers.csv');

        if (! file_exists($path)) {

            $this->error("CSV file not found: {$path}");

            return;
        }

        $handle = fopen($path, 'r');

        if (! $handle) {

            $this->error('Unable to open CSV');

            return;
        }

        $header = fgetcsv($handle);

        if (! $header) {

            $this->error('CSV is empty');

            fclose($handle);

            return;
        }

        $this->info('CSV Loaded...');
        $this->info('Starting Ultra Fast Sync...');

        $carriers = [];

        $count = 0;

        $failed = 0;

        while (($row = fgetcsv($handle)) !== false) {

            try {

                if (count($header) !== count($row)) {
                    continue;
                }

                $data = array_combine($header, $row);

                $dotNumber = $this->number($data['DOT Number'] ?? null);

                if (empty($dotNumber)) {
                    continue;
                }

                $now = now();

                $carriers[] = [

                    'dot_number' => $dotNumber,

                    'docket_number' => $this->number($data['Docket Number'] ?? null),

                    'legal_name' => $this->string($data['Legal Name'] ?? null),

                    'dba_name' => $this->string($data['DBA Name'] ?? null),

                    'telephone_number' => $this->phone($data['Business Phone'] ?? null),

                    'cellphone_number' => $this->phone($data['Cell Phone'] ?? null),

                    'company_contact_primary' => $this->string($data['Company Representative 1'] ?? null),

                    'company_contact_secondary' => $this->string($data['Company Representative 2'] ?? null),

                    'email_address' => $this->string($data['Email Address'] ?? null),

                    'usdot_status' => $this->string($data['Safety Rating Code'] ?? null),

                    'safety_rating_effective_date' => $this->date($data['Safety Rating Effective Date'] ?? null),

                    'created_at' => $now,

                    'updated_at' => $now,
                ];

                if (count($carriers) >= $this->batchSize) {

                    $this->insertBatch($carriers);

                    $count += count($carriers);

                    $this->info("Imported: {$count}");

                    $carriers = [];

                    unset($row, $data);

                    gc_collect_cycles();
                }

            } catch (\Throwable $e) {

                $failed++;

                $this->error(
                    'FAILED => '.
                    ($data['DOT Number'] ?? 'N/A').
                    ' => '.
                    $e->getMessage()
                );
            }
        }

        if (! empty($carriers)) {

            $this->insertBatch($carriers);

            $count += count($carriers);

            unset($carriers);

            gc_collect_cycles();
        }

        fclose($handle);

        $this->info('===================================');
        $this->info("SUCCESS: {$count}");
        $this->info("FAILED: {$failed}");
        $this->info('IMPORT COMPLETED');
        $this->info('===================================');
    }

    private function insertBatch(array $carriers): void
    {
        DB::table('carriers')->upsert(

            $carriers,

            ['dot_number'],

            [
                'docket_number',
                'legal_name',
                'dba_name',
                'telephone_number',
                'cellphone_number',
                'company_contact_primary',
                'company_contact_secondary',
                'email_address',
                'usdot_status',
                'safety_rating_effective_date',
                'updated_at',
            ]
        );
    }

    private function string($value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }

    private function number($value): ?string
    {
        if ($value === '' || $value === null) {
            return null;
        }

        $value = preg_replace('/[^0-9]/', '', (string) $value);

        return $value !== '' ? $value : null;
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

        try {

            return date('Y-m-d', strtotime($value));

        } catch (\Throwable $e) {

            return null;
        }
    }
}
