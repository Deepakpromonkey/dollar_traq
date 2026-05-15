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
        )
            ->select('id', 'dot_number')
            ->cursor();

        Log::channel('fmcsa')->info(
            '======================================'
        );

        Log::channel('fmcsa')->info(
            'NEW FMCSA BATCH STARTED'
        );

        Log::channel('fmcsa')->info(
            '======================================'
        );

        foreach ($carriers as $carrier) {

            try {

                DB::table('carrier_sync_states')->updateOrInsert(
                    [
                        'carrier_id' => $carrier->id,
                    ],
                    [
                        'is_syncing' => true,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                echo PHP_EOL;

                echo '===================================='.PHP_EOL;

                echo 'PROCESSING DOT: '.$carrier->dot_number.PHP_EOL;

                echo 'TIME: '.now().PHP_EOL;

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

                Log::channel('fmcsa')->info(
                    "API RESPONSE => {$response}"
                );

                if (! $response->successful()) {

                    Log::channel('fmcsa')->error(
                        "API FAILED => DOT {$carrier->dot_number}"
                    );

                    Log::channel('fmcsa')->error(
                        'STATUS => '.$response->status()
                    );

                    DB::table('carrier_sync_states')
                        ->where('carrier_id', $carrier->id)
                        ->update([
                            'is_syncing' => false,
                            'updated_at' => now(),
                        ]);

                    continue;
                }

                $json = $response->json();

                if (! isset($json['content']['carrier'])) {

                    Log::channel('fmcsa')->error(
                        "INVALID RESPONSE => {$carrier->dot_number}"
                    );

                    DB::table('carrier_sync_states')
                        ->where('carrier_id', $carrier->id)
                        ->update([
                            'is_syncing' => false,
                            'updated_at' => now(),
                        ]);

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

                $existingCarrier = DB::table('carriers')
                    ->where('id', $carrier->id)
                    ->first();

                $oldValues = (array) $existingCarrier;

                /*
                |--------------------------------------------------------------------------
                | carriers
                |--------------------------------------------------------------------------
                */

                $updateData = [

                    'ein' => $apiCarrier['ein'] ?? null,

                    'legal_name' => $apiCarrier['legalName'] ?? null,

                    'dot_number' => $apiCarrier['dotNumber'] ?? null,

                    'dba_name' => $apiCarrier['dbaName'] ?? null,

                    'allowed_to_operate' => $apiCarrier['allowedToOperate'] ?? null,

                    'status_code' => $apiCarrier['statusCode'] ?? null,

                    'mcs150_outdated' => ($apiCarrier['mcs150Outdated'] ?? 'N') === 'Y',

                    'passenger_carrier' => ($apiCarrier['isPassengerCarrier'] ?? 'N') === 'Y',

                    'authority_broker' => $apiCarrier['brokerAuthorityStatus'] ?? null,

                    'authority_common' => $apiCarrier['commonAuthorityStatus'] ?? null,

                    'authority_contract' => $apiCarrier['contractAuthorityStatus'] ?? null,

                    'latest_review_type_desce' => $apiCarrier['reviewType'] ?? null,

                    'safety_review_type' => $apiCarrier['safetyReviewType'] ?? null,

                    'total_drivers' => $apiCarrier['totalDrivers'] ?? null,

                    'phy_country' => $apiCarrier['phyCountry'] ?? null,

                    'phy_city' => $apiCarrier['phyCity'] ?? null,

                    'phy_state' => $apiCarrier['phyState'] ?? null,

                    'phy_street' => $apiCarrier['phyStreet'] ?? null,

                    'phy_zipcode' => $apiCarrier['phyZipcode'] ?? null,

                    'latest_review_date' => $this->date(
                        $apiCarrier['reviewDate'] ?? null
                    ),

                    'safety_rating' => $apiCarrier['safetyRating'] ?? null,

                    'safety_review_date' => $this->date(
                        $apiCarrier['safetyReviewDate'] ?? null
                    ),

                    'driver_oos_rate' => $apiCarrier['driverOosRate'] ?? null,

                    'hazmat_oos_rate' => $apiCarrier['hazmatOosRate'] ?? null,

                    'vehicle_oos_rate' => $apiCarrier['vehicleOosRate'] ?? null,

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

                    'iss_value' => $this->date(
                        $apiCarrier['issScore'] ?? null
                    ),

                    'updated_at' => now(),
                ];

                DB::table('carriers')
                    ->where('id', $carrier->id)
                    ->update($updateData);

                /*
                |--------------------------------------------------------------------------
                | carrier_crashes
                |--------------------------------------------------------------------------
                */

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

                /*
                |--------------------------------------------------------------------------
                | carrier_operations
                |--------------------------------------------------------------------------
                */

                DB::table('carrier_operations')->updateOrInsert(
                    [
                        'carrier_id' => $carrier->id,
                    ],
                    [
                        'carrier_operation_code' => $apiCarrier['carrierOperation']['carrierOperationCode'] ?? null,

                        'carrier_operation_desc' => $apiCarrier['carrierOperation']['carrierOperationDesc'] ?? null,

                        'updated_at' => now(),

                        'created_at' => now(),
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | carrier_census_types
                |--------------------------------------------------------------------------
                */

                DB::table('carrier_census_types')->updateOrInsert(
                    [
                        'carrier_id' => $carrier->id,
                    ],
                    [
                        'census_type' => $apiCarrier['censusTypeId']['censusType'] ?? null,

                        'census_type_desc' => $apiCarrier['censusTypeId']['censusTypeDesc'] ?? null,

                        'census_type_id' => $apiCarrier['censusTypeId']['censusTypeId'] ?? null,

                        'updated_at' => now(),

                        'created_at' => now(),
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | carrier_inspections
                |--------------------------------------------------------------------------
                */

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

                        'driverOosRate' => $apiCarrier['driverOosRate'] ?? null,

                        'updated_at' => now(),

                        'created_at' => now(),
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | carrier_fleet_summaries
                |--------------------------------------------------------------------------
                */

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

                /*
                |--------------------------------------------------------------------------
                | carrier_insurances
                |--------------------------------------------------------------------------
                */

                DB::table('carrier_insurances')->updateOrInsert(

                    [
                        'carrier_id' => $carrier->id,
                    ],

                    [
                        'insurance_bipd_on_file' => $apiCarrier['bipdInsuranceOnFile'] ?? null,

                        'insurance_bipd_required' => $apiCarrier['bipdInsuranceRequired'] ?? null,

                        'insurance_cargo_required' => $apiCarrier['cargoInsuranceRequired'] ?? null,

                        'bipdRequiredAmount' => $apiCarrier['bipdRequiredAmount'] ?? null,

                        'insurance_bond_on_file' => $apiCarrier['bondInsuranceOnFile'] ?? null,

                        'insurance_bond_required' => $apiCarrier['bondInsuranceRequired'] ?? null,

                        'insurance_cargo_on_file' => $apiCarrier['cargoInsuranceOnFile'] ?? null,

                        'updated_at' => now(),

                        'created_at' => now(),
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | change logs
                |--------------------------------------------------------------------------
                */

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

                /*
                |--------------------------------------------------------------------------
                | sync states
                |--------------------------------------------------------------------------
                */

                DB::table('carrier_sync_states')->updateOrInsert(
                    [
                        'carrier_id' => $carrier->id,
                    ],
                    [
                        'last_synced_at' => now(),

                        'last_success_at' => now(),

                        'next_sync_at' => now()->addDays(30),

                        'is_syncing' => false,

                        'updated_at' => now(),

                        'created_at' => now(),
                    ]
                );

                DB::table('carrier_sync_states')
                    ->where('carrier_id', $carrier->id)
                    ->increment('sync_attempts');

                /*
                |--------------------------------------------------------------------------
                | sync logs
                |--------------------------------------------------------------------------
                */

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

                DB::table('carrier_sync_states')
                    ->where('carrier_id', $carrier->id)
                    ->update([
                        'is_syncing' => false,
                        'updated_at' => now(),
                    ]);

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
