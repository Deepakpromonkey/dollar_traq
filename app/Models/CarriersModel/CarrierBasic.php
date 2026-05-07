<?php

namespace App\Models\CarriersModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarrierBasic extends Model
{
    protected $fillable = [
        'carrier_id',
        'row_id',
        'violations_oos_unsafe_driving',
        'basic_alert_unsafe_driving',
        'violations_vehicle_maintence',
        'violations_severe_vehicle_maintence',
        'violations_oos_vehicle_maintence',
        'basic_alert_vehicle_maintence',
        'violations_controlled_substance',
        'violations_severe_controlled_substance',
        'violations_oos_controlled_substance',
        'basic_alert_controlled_substance',
        'violations_driver_fitness',
        'violations_severe_driver_fitness',
        'violations_oos_driver_fitness',
        'basic_alert_driver_fitness',
    ];

    protected $casts = [
        'basic_alert_unsafe_driving' => 'boolean',
        'basic_alert_vehicle_maintence' => 'boolean',
        'basic_alert_controlled_substance' => 'boolean',
        'basic_alert_driver_fitness' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class, 'carrier_id', 'id');
    }
}
