<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportCarrierCsv extends Command
{
    protected $signature = 'import:carriers';

    protected $description = 'Import carriers CSV into all related tables';

    public function handle()
    {
        ini_set('memory_limit', '-1');

        $path = storage_path('app/public/carriers.csv');

        if (!file_exists($path)) {

            $this->error("CSV file not found: {$path}");
            return;
        }

        $handle = fopen($path, 'r');

        if (!$handle) {

            $this->error("Unable to open CSV");
            return;
        }

        $header = fgetcsv($handle);

        if (!$header) {

            $this->error("CSV is empty");
            fclose($handle);
            return;
        }

        $this->info('CSV Loaded Successfully');
        $this->info('Starting Import...');

        $count = 0;
        $failed = 0;

        while (($row = fgetcsv($handle)) !== false) {

            try {

                if (count($header) != count($row)) {
                    continue;
                }

                $data = array_combine($header, $row);

                $dotNumber = trim($data['DOT Number'] ?? '');

                if (empty($dotNumber)) {
                    continue;
                }

                DB::transaction(function () use ($data, &$count) {

                    /*
                    |--------------------------------------------------------------------------
                    | INSERT CARRIER
                    |--------------------------------------------------------------------------
                    */

                    $carrierId = DB::table('carriers')->insertGetId([

                        'row_id' => Str::uuid(),

                        'dot_number' => $this->number($data['DOT Number'] ?? null),

                        'docket_number' => $this->number($data['Docket Number'] ?? null),

                        'legal_name' => $this->string($data['Legal Name'] ?? null),

                        'dba_name' => $this->string($data['DBA Name'] ?? null),

                        'telephone_number' => $this->phone($data['Business Phone'] ?? null),

                        'cellphone_number' => $this->phone($data['Cell Phone'] ?? null),

                        'company_contact_primary' => $this->string($data['Company Representative 1'] ?? null),

                        'company_contact_secondary' => $this->string($data['Company Representative 2'] ?? null),

                        'email_address' => $this->string($data['Email Address'] ?? null),

                        'usdot_status' => $this->string($data['Safety Rating Code'] ?? null),

                        'safety_rating_effective_date' =>
                            $this->date($data['Safety Rating Effective Date'] ?? null),

                        'created_at' => now(),

                        'updated_at' => now(),
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | FLEET SUMMARY
                    |--------------------------------------------------------------------------
                    */

                    $fleetId = DB::table('carrier_fleet_summaries')->insertGetId([

                        'row_id' => Str::uuid(),

                        'carrier_id' => $carrierId,

                        'total_trucks' =>
                            $this->number($data['Total Number Of Trucks'] ?? null),

                        'total_power_units' =>
                            $this->number($data['Total Number Of Power Units'] ?? null),

                        'created_at' => now(),

                        'updated_at' => now(),
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | INSURANCE
                    |--------------------------------------------------------------------------
                    */

                    $insuranceId = DB::table('carrier_insurances')->insertGetId([

                        'row_id' => Str::uuid(),

                        'carrier_id' => $carrierId,

                        'insurance_bipd_on_file' =>
                            $this->number($data['BIPD On File'] ?? null),

                        'insurance_cargo_on_file' =>
                            $this->number($data['Cargo On File'] ?? null),

                        'insurance_bond_on_file' =>
                            $this->number($data['Bond Surety On File'] ?? null),

                        'created_at' => now(),

                        'updated_at' => now(),
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | BASICS
                    |--------------------------------------------------------------------------
                    */

                    DB::table('carrier_basics')->insert([

                        'row_id' => Str::uuid(),

                        'carrier_id' => $carrierId,

                        'created_at' => now(),

                        'updated_at' => now(),
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | CRASHES
                    |--------------------------------------------------------------------------
                    */

                    DB::table('carrier_crashes')->insert([

                        'row_id' => Str::uuid(),

                        'carrier_id' => $carrierId,

                        'created_at' => now(),

                        'updated_at' => now(),
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | INSPECTIONS
                    |--------------------------------------------------------------------------
                    */

                    DB::table('carrier_inspections')->insert([

                        'row_id' => Str::uuid(),

                        'carrier_id' => $carrierId,

                        'created_at' => now(),

                        'updated_at' => now(),
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | CONTACT CHANGES
                    |--------------------------------------------------------------------------
                    */

                    $contactChangeId = DB::table('carrier_contact_changes')->insertGetId([

                        'row_id' => Str::uuid(),

                        'carrier_id' => $carrierId,

                        'created_at' => now(),

                        'updated_at' => now(),
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | CONTACT HISTORY
                    |--------------------------------------------------------------------------
                    */

                    DB::table('carrier_contact_histories')->insert([

                        'row_id' => Str::uuid(),

                        'carrier_id' => $carrierId,

                        'physical_address_city' =>
                            $this->string($data['Business City'] ?? null),

                        'physical_address_state' =>
                            $this->string($data['Business State'] ?? null),

                        'created_at' => now(),

                        'updated_at' => now(),
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | COMPANY SNAPSHOT
                    |--------------------------------------------------------------------------
                    */

                    DB::table('carrier_company_snapshots')->insert([

                        'row_id' => Str::uuid(),

                        'carrier_id' => $carrierId,

                        'created_at' => now(),

                        'updated_at' => now(),
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | CONTACT HISTORY LOG
                    |--------------------------------------------------------------------------
                    */

                    DB::table('carrier_contact_history_logs')->insert([

                        'row_id' => Str::uuid(),

                        'contact_change_id' => $contactChangeId,

                        'created_at' => now(),
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | ADDRESS IDS
                    |--------------------------------------------------------------------------
                    */

                    DB::table('carrier_address_ids')->insert([

                        'row_id' => Str::uuid(),

                        'contact_change_id' => $contactChangeId,

                        'created_at' => now(),
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | INSURANCE HISTORY
                    |--------------------------------------------------------------------------
                    */

                    DB::table('carrier_insurance_histories')->insert([

                        'row_id' => Str::uuid(),

                        'insurance_id' => $insuranceId,

                        'created_at' => now(),
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | EQUIPMENT HISTORY
                    |--------------------------------------------------------------------------
                    */

                    DB::table('carrier_equipment_histories')->insert([

                        'row_id' => Str::uuid(),

                        'fleet_id' => $fleetId,

                        'created_at' => now(),
                    ]);

                    $count++;

                    $this->info("Imported Carrier DOT: " . ($data['DOT Number'] ?? 'N/A'));
                });

            } catch (\Exception $e) {

                $failed++;

                $this->error(
                    "Failed DOT " .
                    ($data['DOT Number'] ?? 'N/A') .
                    " => " .
                    $e->getMessage()
                );
            }
        }

        fclose($handle);

        $this->info("====================================");
        $this->info("IMPORT COMPLETED");
        $this->info("SUCCESS: {$count}");
        $this->info("FAILED: {$failed}");
        $this->info("====================================");
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    private function string($value)
    {
        return !empty($value)
            ? trim($value)
            : null;
    }

    private function number($value)
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return preg_replace('/[^0-9]/', '', $value);
    }

    private function phone($value)
    {
        if (empty($value)) {
            return null;
        }

        return preg_replace('/[^0-9]/', '', $value);
    }

    private function date($value)
    {
        if (empty($value)) {
            return null;
        }

        try {

            return date('Y-m-d', strtotime($value));

        } catch (\Exception $e) {

            return null;
        }
    }
}