<?php

namespace App\Models\CarriersModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarrierCrash extends Model
{
    protected $fillable = [
         'carrier_id',
        'row_id',
        'crash_fatalities',
        'crash_injuries',
        'crashes_tow_away',
        'crashes_total',
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
