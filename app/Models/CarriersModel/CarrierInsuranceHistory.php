<?php

namespace App\Models\CarriersModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarrierInsuranceHistory extends Model
{
    protected $fillable = [
        'insurance_id',
         'carrier_id',
        'row_id',
        'insurance_status',
        'insurance_form_code',
        'insurance_type_code',
        'insurance_carrier',
        'policy_number',
        'effective_date',
        'cancel_method',
        'cancel_effective_date',
        'underlying_limit_amount',
        'minimum_coverage_amount',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    public function insurance(): BelongsTo
    {
        return $this->belongsTo(CarrierInsurance::class, 'insurance_id', 'id');
    }
}
