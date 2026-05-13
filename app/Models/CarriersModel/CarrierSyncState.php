<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarrierSyncState extends Model
{
    protected $fillable = [
        'carrier_id',
        'last_synced_at',
        'next_sync_at',
        'sync_attempts',
        'is_syncing',
        'last_success_at',
        'last_hash',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
        'next_sync_at' => 'datetime',
        'last_success_at' => 'datetime',
        'is_syncing' => 'boolean',
    ];

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }
}