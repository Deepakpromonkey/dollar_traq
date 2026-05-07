<?php

namespace App\Models\CarriersModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarrierInspection extends Model
{
    protected $fillable = [
         'carrier_id',
        'row_id',
        'inspections_vehicle',
        'inspections_vehicle_out_of_service',
        'inspections_driver',
        'inspections_driver_out_of_service',
        'inspections_hazmat',
        'inspections_hazmat_out_of_service',
        'natl_avg_oos_vehicle',
        'natl_avg_oos_driver',
        'natl_avg_oos_hazmat',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class, 'carrier_id', 'id');
    }
}
