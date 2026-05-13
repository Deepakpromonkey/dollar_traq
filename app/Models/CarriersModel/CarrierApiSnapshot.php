<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarrierApiSnapshot extends Model
{
    protected $fillable = [
        'carrier_id',
        'response_json',
        'fetched_at',
    ];

    protected $casts = [
        'fetched_at' => 'datetime',
    ];

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }
}