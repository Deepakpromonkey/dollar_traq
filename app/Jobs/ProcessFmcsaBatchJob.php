<?php

namespace App\Jobs;

use App\Models\CarriersModel\Carrier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessFmcsaBatchJob implements ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    public $tries = 3;

    public $timeout = 0;

    public array $carrierIds;

    public function __construct(array $carrierIds)
    {
        $this->carrierIds = $carrierIds;
    }

    public function handle(): void
    {
        ini_set('memory_limit', '4024M');

        DB::connection()->disableQueryLog();

        $carriers = Carrier::whereIn(
            'id',
            $this->carrierIds
        )->get();

        Log::channel('fmcsa')->info(
            '======================================'
        );

        Log::channel('fmcsa')->info(
            'NEW FMCSA BATCH STARTED'
        );

        Log::channel('fmcsa')->info(
            'TOTAL CARRIERS: '.count($carriers)
        );

        Log::channel('fmcsa')->info(
            '======================================'
        );

        foreach ($carriers as $index => $carrier) {

            try {

                echo PHP_EOL;

                echo '===================================='.PHP_EOL;

                echo 'PROCESSING DOT: '.
                    $carrier->dot_number.PHP_EOL;

                echo 'TIME: '.
                    now().PHP_EOL;

                echo '===================================='.PHP_EOL;

                $url =
                    'https://mobile.fmcsa.dot.gov/qc/services/carriers/'.
                    $carrier->dot_number.
                    '?webKey='.
                    config('services.fmcsa.key');

                Log::channel('fmcsa')->info(
                    "API URL => {$url}"
                );

                $response = Http::timeout(60)
                    ->acceptJson()
                    ->get($url);

                if (! $response->successful()) {

                    Log::channel('fmcsa')->error(
                        "API FAILED => DOT {$carrier->dot_number}"
                    );

                    Log::channel('fmcsa')->error(
                        'STATUS => '.$response->status()
                    );

                    continue;
                }

                $json = $response->json();

                Log::channel('fmcsa')->info(
                    json_encode(
                        $json,
                        JSON_PRETTY_PRINT
                    )
                );

                if (! isset($json['content']['carrier'])) {

                    Log::channel('fmcsa')->error(
                        "INVALID RESPONSE => {$carrier->dot_number}"
                    );

                    continue;
                }

                $apiCarrier = $json['content']['carrier'];

                DB::table('carrier_api_snapshots')->insert([

                    'carrier_id' => $carrier->id,

                    'response_json' => json_encode($json),

                    'fetched_at' => now(),

                    'created_at' => now(),

                    'updated_at' => now(),
                ]);

                $oldValues = $carrier->toArray();

                $updateData = [

                    'legal_name' => $apiCarrier['legalName'] ?? null,

                    'dba_name' => $apiCarrier['dbaName'] ?? null,

                    'allowed_to_operate' => $apiCarrier['allowedToOperate'] ?? null,

                    'status_code' => $apiCarrier['statusCode'] ?? null,

                    'usdot_status' => $apiCarrier['statusCode'] ?? null,

                    'mcs150_outdated' => ($apiCarrier['mcs150Outdated'] ?? 'N') === 'Y',

                    'passenger_carrier' => ($apiCarrier['isPassengerCarrier'] ?? 'N') === 'Y',

                    'broker_authority_status' => $apiCarrier['brokerAuthorityStatus'] ?? null,

                    'common_authority_status' => $apiCarrier['commonAuthorityStatus'] ?? null,

                    'contract_authority_status' => $apiCarrier['contractAuthorityStatus'] ?? null,

                    'review_type' => $apiCarrier['reviewType'] ?? null,

                    'safety_review_type' => $apiCarrier['safetyReviewType'] ?? null,

                    'total_drivers' => $apiCarrier['totalDrivers'] ?? null,

                    'phy_country' => $apiCarrier['phyCountry'] ?? null,

                    'oos_rate_national_average_year' => $apiCarrier['oosRateNationalAverageYear'] ?? null,

                    'oos_date' => $this->date(
                        $apiCarrier['oosDate'] ?? null
                    ),

                    'safety_rating_date' => $this->date(
                        $apiCarrier['safetyRatingDate'] ?? null
                    ),

                    'snapshot_date' => $this->date(
                        $apiCarrier['snapshotDate'] ?? null
                    ),

                    'updated_at' => now(),
                ];

                DB::table('carriers')
                    ->where('id', $carrier->id)
                    ->update($updateData);

                DB::table('carrier_crashes')->updateOrInsert(

                    [
                        'carrier_id' => $carrier->id,
                    ],

                    [
                        'crash_fatalities' => $apiCarrier['fatalCrash'] ?? 0,

                        'crash_injuries' => $apiCarrier['injCrash'] ?? 0,

                        'crashes_tow_away' => $apiCarrier['towawayCrash'] ?? 0,

                        'crashes_total' => $apiCarrier['crashTotal'] ?? 0,

                        'updated_at' => now(),

                        'created_at' => now(),
                    ]
                );

                DB::table('carrier_inspections')->updateOrInsert(

                    [
                        'carrier_id' => $carrier->id,
                    ],

                    [
                        'inspections_vehicle' => $apiCarrier['vehicleInsp'] ?? 0,

                        'inspections_vehicle_out_of_service' => $apiCarrier['vehicleOosInsp'] ?? 0,

                        'inspections_driver' => $apiCarrier['driverInsp'] ?? 0,

                        'inspections_driver_out_of_service' => $apiCarrier['driverOosInsp'] ?? 0,

                        'inspections_hazmat' => $apiCarrier['hazmatInsp'] ?? 0,

                        'inspections_hazmat_out_of_service' => $apiCarrier['hazmatOosInsp'] ?? 0,

                        'natl_avg_oos_vehicle' => $apiCarrier['vehicleOosRateNationalAverage'] ?? null,

                        'natl_avg_oos_driver' => $apiCarrier['driverOosRateNationalAverage'] ?? null,

                        'natl_avg_oos_hazmat' => $apiCarrier['hazmatOosRateNationalAverage'] ?? null,

                        'updated_at' => now(),

                        'created_at' => now(),
                    ]
                );

                DB::table('carrier_fleet_summaries')->updateOrInsert(

                    [
                        'carrier_id' => $carrier->id,
                    ],

                    [
                        'total_power_units' => $apiCarrier['totalPowerUnits'] ?? 0,

                        'updated_at' => now(),

                        'created_at' => now(),
                    ]
                );

                DB::table('carrier_insurances')->updateOrInsert(

                    [
                        'carrier_id' => $carrier->id,
                    ],

                    [
                        'insurance_bipd_on_file' => $apiCarrier['bipdInsuranceOnFile'] ?? null,

                        'insurance_bond_on_file' => $apiCarrier['bondInsuranceOnFile'] ?? null,

                        'insurance_cargo_on_file' => $apiCarrier['cargoInsuranceOnFile'] ?? null,

                        'insurance_bipd_required' => $apiCarrier['bipdRequiredAmount'] ?? null,

                        'updated_at' => now(),

                        'created_at' => now(),
                    ]
                );

                $totalChanges = 0;

                foreach ($updateData as $column => $newValue) {

                    $oldValue = $oldValues[$column] ?? null;

                    if ((string) $oldValue !== (string) $newValue) {

                        $totalChanges++;

                        DB::table('carrier_change_logs')->insert([

                            'carrier_id' => $carrier->id,

                            'table_name' => 'carriers',

                            'column_name' => $column,

                            'old_value' => is_array($oldValue)
                                ? json_encode($oldValue)
                                : $oldValue,

                            'new_value' => is_array($newValue)
                                ? json_encode($newValue)
                                : $newValue,

                            'synced_at' => now(),

                            'created_at' => now(),

                            'updated_at' => now(),
                        ]);
                    }
                }

                DB::table('carrier_sync_states')->updateOrInsert(

                    [
                        'carrier_id' => $carrier->id,
                    ],

                    [
                        'last_synced_at' => now(),

                        'last_success_at' => now(),

                        'next_sync_at' => now()->addDays(30),

                        'sync_attempts' => DB::raw('sync_attempts + 1'),

                        'is_syncing' => false,

                        'updated_at' => now(),

                        'created_at' => now(),
                    ]
                );

                DB::table('carrier_sync_logs')->insert([

                    'carrier_id' => $carrier->id,

                    'status' => 'success',

                    'total_changes' => $totalChanges,

                    'message' => 'Carrier synced successfully',

                    'api_response' => json_encode($json),

                    'started_at' => now(),

                    'completed_at' => now(),

                    'created_at' => now(),

                    'updated_at' => now(),
                ]);

                Log::channel('fmcsa')->info(
                    "SYNC SUCCESS => DOT {$carrier->dot_number}"
                );

            } catch (\Throwable $e) {

                Log::channel('fmcsa')->error(
                    "SYNC FAILED => DOT {$carrier->dot_number}"
                );

                Log::channel('fmcsa')->error(
                    $e->getMessage()
                );

                DB::table('carrier_sync_logs')->insert([

                    'carrier_id' => $carrier->id,

                    'status' => 'failed',

                    'message' => $e->getMessage(),

                    'started_at' => now(),

                    'completed_at' => now(),

                    'created_at' => now(),

                    'updated_at' => now(),
                ]);
            }

            usleep(100000);
        }

        Log::channel('fmcsa')->info(
            'BATCH COMPLETED => Sleeping 3 minutes...'
        );

        sleep(180);

        Log::channel('fmcsa')->info(
            '======================================'
        );

        Log::channel('fmcsa')->info(
            'BATCH FINISHED'
        );

        Log::channel('fmcsa')->info(
            '======================================'
        );
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
