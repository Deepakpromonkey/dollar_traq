<?php

namespace App\Models\CarriersModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarrierContactHistoryLog extends Model
{
    protected $fillable = [
        'contact_change_id',
         'carrier_id',
        'row_id',
        'history_key',
        'history_value',
        'start_date',
        'end_date',
        'current_flag',
    ];

    protected $casts = [
        'current_flag' => 'boolean',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    public function contactChange(): BelongsTo
    {
        return $this->belongsTo(CarrierContactChange::class, 'contact_change_id', 'id');
    }
}
