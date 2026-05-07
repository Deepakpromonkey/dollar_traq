<?php

namespace App\Models\CarriersModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarrierContactChange extends Model
{
    protected $fillable = [
        'carrier_id',
        'row_id',
        'name_change_count',
        'name_last_changed',
        'email_change_count',
        'email_last_changed',
        'phone_change_count',
        'phone_last_changed',
        'address_change_count',
        'address_last_changed',
        'contact_change_count',
        'contact_last_changed',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class, 'carrier_id', 'id');
    }

    public function contactHistoryLogs(): HasMany
    {
        return $this->hasMany(CarrierContactHistoryLog::class, 'contact_change_id', 'id');
    }

    public function addressIds(): HasMany
    {
        return $this->hasMany(CarrierAddressId::class, 'contact_change_id', 'id');
    }
}
