<?php

namespace App\Services;

use App\Models\Carrier;
use App\Models\CarrierCrash;
use App\Models\CarrierInspection;
use App\Models\CarrierInsurance;
use App\Models\CarrierFleetSummary;
use App\Models\CarrierContactHistory;

use App\Models\CarrierChangeLog;
use App\Models\CarrierApiSnapshot;
use App\Models\CarrierSyncLog;
use App\Models\CarrierSyncState;

use Illuminate\Support\Facades\DB;

class CarrierSyncService
{
    protected FmcsaService $fmcsaService;

    protected FmcsaMapperService $mapper;

    public function __construct(
        FmcsaService $fmcsaService,
        FmcsaMapperService $mapper
    ) {
        $this->fmcsaService = $fmcsaService;

        $this->mapper = $mapper;
    }

    public function syncCarrier(Carrier $carrier)
    {
        DB::beginTransaction();

        $syncLog = CarrierSyncLog::create([
            'carrier_id' => $carrier->id,
            'status' => 'started',
            'started_at' => now(),
        ]);

        try {

            $response = $this->fmcsaService
                ->getCarrierByDotNumber($carrier->dot_number);

            CarrierApiSnapshot::create([
                'carrier_id' => $carrier->id,
                'response_json' => json_encode($response),
                'fetched_at' => now(),
            ]);

            $mapped = $this->mapper->map($response);

            $hash = md5(json_encode($mapped));

            $syncState = CarrierSyncState::firstOrCreate([
                'carrier_id' => $carrier->id,
            ]);

            if ($syncState->last_hash === $hash) {

                $syncLog->update([
                    'status' => 'skipped',
                    'message' => 'No changes',
                    'completed_at' => now(),
                ]);

                DB::commit();

                return;
            }

            $totalChanges = 0;

            $totalChanges += $this->syncTable(
                $carrier,
                $mapped['carriers'] ?? []
            );

            $totalChanges += $this->syncRelation(
                CarrierCrash::class,
                'carrier_id',
                $carrier->id,
                $mapped['carrier_crashes'] ?? [],
                'carrier_crashes',
                $carrier->id
            );

            $totalChanges += $this->syncRelation(
                CarrierInspection::class,
                'carrier_id',
                $carrier->id,
                $mapped['carrier_inspections'] ?? [],
                'carrier_inspections',
                $carrier->id
            );

            $totalChanges += $this->syncRelation(
                CarrierInsurance::class,
                'carrier_id',
                $carrier->id,
                $mapped['carrier_insurances'] ?? [],
                'carrier_insurances',
                $carrier->id
            );

            $totalChanges += $this->syncRelation(
                CarrierFleetSummary::class,
                'carrier_id',
                $carrier->id,
                $mapped['carrier_fleet_summaries'] ?? [],
                'carrier_fleet_summaries',
                $carrier->id
            );

            $totalChanges += $this->syncRelation(
                CarrierContactHistory::class,
                'carrier_id',
                $carrier->id,
                $mapped['carrier_contact_histories'] ?? [],
                'carrier_contact_histories',
                $carrier->id
            );

            $syncState->update([
                'last_hash' => $hash,
                'last_synced_at' => now(),
                'last_success_at' => now(),
                'is_syncing' => false,
            ]);

            $syncLog->update([
                'status' => 'success',
                'total_changes' => $totalChanges,
                'completed_at' => now(),
            ]);

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

            $syncLog->update([
                'status' => 'failed',
                'message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }

    protected function syncTable(
        $model,
        array $data
    ) {
        $changes = 0;

        $updateData = [];

        foreach ($data as $column => $newValue) {

            if (!isset($model->$column)) {
                continue;
            }

            $oldValue = $model->$column;

            if ((string)$oldValue !== (string)$newValue) {

                CarrierChangeLog::create([
                    'carrier_id' => $model->id,
                    'table_name' => $model->getTable(),
                    'record_id' => $model->id,
                    'column_name' => $column,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                    'synced_at' => now(),
                ]);

                $updateData[$column] = $newValue;

                $changes++;
            }
        }

        if (!empty($updateData)) {
            $model->update($updateData);
        }

        return $changes;
    }

    protected function syncRelation(
        $modelClass,
        $foreignKey,
        $foreignValue,
        array $data,
        $tableName,
        $carrierId
    ) {
        $model = $modelClass::firstOrCreate([
            $foreignKey => $foreignValue,
        ]);

        $changes = 0;

        $updateData = [];

        foreach ($data as $column => $newValue) {

            $oldValue = $model->$column;

            if ((string)$oldValue !== (string)$newValue) {

                CarrierChangeLog::create([
                    'carrier_id' => $carrierId,
                    'table_name' => $tableName,
                    'record_id' => $model->id,
                    'column_name' => $column,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                    'synced_at' => now(),
                ]);

                $updateData[$column] = $newValue;

                $changes++;
            }
        }

        if (!empty($updateData)) {

            $model->update($updateData);
        }

        return $changes;
    }
}