<?php

namespace App\Models\CarriersModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FmcsaField extends Model
{
    use HasFactory;

    protected $table = 'fmcsa_fields';

    protected $fillable = [
        'carrier_id',
        'allowed_to_operate',
        'common_authority_status',
        'contract_authority_status',
        'phy_country',
        'review_type',
        'safety_review_type',
        'status_code',
        'crash_total',
        'driver_insp',
        'driver_oos_insp',
        'fatal_crash',
        'hazmat_insp',
        'hazmat_oos_insp',
        'inj_crash',
        'iss_score',
        'towaway_crash',
        'vehicle_insp',
        'vehicle_oos_insp',
        'vehicle_oos_rate_national_average',
    ];

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }
}
