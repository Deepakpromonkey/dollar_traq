<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarrierChangeLog extends Model
{
    protected $fillable = [
        'carrier_id',
        'table_name',
        'record_id',
        'column_name',
        'old_value',
        'new_value',
        'synced_at',
    ];

    protected $casts = [
        'synced_at' => 'datetime',
    ];

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }
}