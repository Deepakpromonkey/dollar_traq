<?php

namespace App\Models\CarriersModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarrierFleetSummary extends Model
{
    protected $fillable = [
        'carrier_id',
        'row_id',
        'total_power_units',
        'total_trucks',
        'total_trailers',
        'owned_trailers',
        'inspected_power_units',
        'inspected_trailers',
        'observed_IM',
        'observed_IRPU',
        'observed_PUM',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class, 'carrier_id', 'id');
    }

    public function equipmentHistories(): HasMany
    {
        return $this->hasMany(CarrierEquipmentHistory::class, 'fleet_id', 'id');
    }
}
