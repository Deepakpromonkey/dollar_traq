<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarrierSyncLog extends Model
{
    protected $fillable = [
        'carrier_id',
        'status',
        'total_changes',
        'message',
        'api_response',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }
}