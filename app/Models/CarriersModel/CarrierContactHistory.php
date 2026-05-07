<?php

namespace App\Models\CarriersModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarrierContactHistory extends Model
{
    protected $fillable = [
        'carrier_id',
        'row_id',
        'physical_address',
        'physical_address_city',
        'physical_address_state',
        'physical_address_street',
        'physical_address_zip_code',
        'physical_address_iso_country_code',
        'undeliverable_physical_address',
        'physical_address_authority',
        'physical_address_authority_city',
        'physical_address_authority_state',
        'physical_address_authority_street',
        'physical_address_authority_zip_code',
        'mailing_address',
        'mailing_address_city',
        'mailing_address_state',
        'mailing_address_street',
        'mailing_address_zip_code',
        'mailing_address_iso_country_code',
        'undeliverable_mailing_address',
        'mailing_address_authority',
        'mailing_address_authority_city',
        'mailing_address_authority_state',
        'mailing_address_authority_street',
        'mailing_address_authority_zip_code',
    ];

    protected $casts = [
        'undeliverable_physical_address' => 'boolean',
        'undeliverable_mailing_address' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class, 'carrier_id', 'id');
    }
}
