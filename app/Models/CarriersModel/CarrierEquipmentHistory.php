<?php

namespace App\Models\CarriersModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarrierEquipmentHistory extends Model
{
    protected $fillable = [
        'fleet_id',
         'carrier_id',
        'row_id',
        'vin',
        'type',
        'unit_type_desc',
        'inspections_total_vin',
        'inspections_total_vin_dot',
        'countd_dots_vin',
        'multi_dot_flag',
        'last_inspection_date_vin',
        'last_inspection_date_vin_dot',
        'last_inspected_under_dot',
        'most_recent_inspection_flag',
    ];

    protected $casts = [
        'multi_dot_flag' => 'boolean',
        'most_recent_inspection_flag' => 'boolean',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    public function fleetSummary(): BelongsTo
    {
        return $this->belongsTo(CarrierFleetSummary::class, 'fleet_id', 'id');
    }
}
