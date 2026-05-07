<?php

namespace App\Models\CarriersModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarrierInsurance extends Model
{
    protected $fillable = [
        'carrier_id',
        'row_id',
        'insurance_cancel_count',
        'insurance_last_canceled',
        'insurance_bipd_on_file',
        'insurance_bipd_required',
        'insurance_bond_on_file',
        'insurance_bond_required',
        'insurance_cargo_on_file',
        'insurance_cargo_required',
    ];

    protected $casts = [
        'insurance_bipd_required' => 'boolean',
        'insurance_bond_required' => 'boolean',
        'insurance_cargo_required' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class, 'carrier_id', 'id');
    }

    public function insuranceHistories(): HasMany
    {
        return $this->hasMany(CarrierInsuranceHistory::class, 'insurance_id', 'id');
    }
}
