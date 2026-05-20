<?php

namespace App\Models\CarriersModel;

use App\Modules\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class Carrier extends BaseModel
{
    protected $fillable = [
        'id',
        'row_id',
        'usdot_status',
        'dot_number',
        'docket',
        'docket_number',
        'docket_prefix',
        'legal_name',
        'dba_name',
        'indicator_insurance',
        'dba_flag',
        'duns',
        'mcs150_year',
        'mcs150_mileage',
        'company_contact_primary',
        'company_contact_secondary',
        'telephone_number',
        'physical_telephone_number_authority',
        'mailing_telephone_number_authority',
        'cellphone_number',
        'fax_number',
        'email_address',
        'email_domain',
        'safety_rating_effective_date',
    ];

    protected $casts = [
        'dba_flag' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function format($row)
    {
        if (! $row) {
            return $row;
        }

        foreach ($row->toArray() as $key => $value) {

            if (
                str_contains($key, 'date') ||
                str_contains($key, 'created_at') ||
                str_contains($key, 'updated_at') ||
                str_contains($key, 'canceled') ||
                str_contains($key, 'changed')
            ) {
                if (! empty($value) && strtotime($value)) {
                    $row->$key = date('d-m-Y', strtotime($value));
                }
            }

            if (
                str_contains($key, 'legal_name') ||
                str_contains($key, 'city') ||
                str_contains($key, 'state') ||
                str_contains($key, 'address') ||
                str_contains($key, 'type') ||
                str_contains($key, 'desc')
            ) {
                if (! empty($value) && is_string($value)) {
                    $row->$key = ucwords($value);
                }
            }

            if (
                str_contains($key, 'amount') ||
                str_contains($key, 'price') ||
                str_contains($key, 'cost') ||
                str_contains($key, 'limit')
            ) {
                if (is_numeric($value)) {
                    $row->$key = number_format((float) $value, 2, '.', '');
                }
            }
        }

        return $row;
    }

    private static function applySearchFilters($query, $request, $searchableFields)
    {
        foreach ($searchableFields as $field) {
            if ($request->filled($field)) {
                $query->where($field, trim($request->{$field}));
            }
        }

        return $query;
    }

    public static function search(Request $request)
    {
        $searchValue = trim($request->query('query', ''));

        $sortParam = $request->query('sort', '');
        $sortDir = match ($sortParam) {
            'sortByNameAsc' => 'asc',
            'sortByNameDesc' => 'desc',
            default => 'asc',
        };

        $query = self::query();

        $searchableFields = [
            'docket_number',
            'dot_number',
            'legal_name',
            'telephone_number',
            'email_address',
            'ein',
            'docket',
            'duns',
        ];

        $applySearch = function ($q) use ($searchableFields, $searchValue) {
            $q->where(function ($inner) use ($searchableFields, $searchValue) {

                $searchValue = trim($searchValue);
                $isNumeric = is_numeric($searchValue);

                foreach ($searchableFields as $field) {

                    if ($isNumeric) {
                        $inner->orWhere($field, $searchValue);
                    } else {
                        $inner->orWhereRaw(
                            "LOWER($field) LIKE ?",
                            ['%'.strtolower($searchValue).'%']
                        );
                    }
                }
            });
        };

        if (! empty($searchValue)) {
            $applySearch($query);
        }

        if (! $query->exists()) {
            self::fetchAndStore($request);
            $query = self::query();

            if (! empty($searchValue)) {
                $applySearch($query);
            }
        }

        $data = $query
            ->select([
                'id',
                'row_id',
                'dot_number',
                'entity_type_desc',
                'docket_number',
                'docket',
                'ein',
                'duns',
                'legal_name',
                'telephone_number',
                'email_address',
                'mcs150_mileage',
                'risk_score',
                'authority_contract',
                'authority_broker',
                'indicator_insurance',
                'usdot_status',
            ])
            ->with([
                'fleetSummaries:id,carrier_id,total_power_units',
                'contactHistories:id,carrier_id,physical_address',
            ])
            ->orderBy('legal_name', $sortDir)
            ->paginate($request->query('per_page', 10));

        $data->getCollection()->transform(function ($row) {
            return [
                'id' => $row->id,
                'raw_id' => $row->row_id,
                'carrier_type' => $row->entity_type_desc,
                'company_name' => $row->legal_name,
                'mileage' => $row->mcs150_mileage,
                'usdot_status' => $row->usdot_status,
                'mc_number' => $row->docket_number,
                'dot_number' => $row->dot_number,
                'ein' => $row->ein,
                'duns' => $row->duns,
                'phone' => $row->telephone_number,
                'email' => $row->email_address,
                'authority_verified' => $row->authority_contract === 'Active',
                'insurance_current' => (bool) $row->indicator_insurance,
                'risk_level' => $row->risk_score,
            ];
        });

        return [
            'total_results' => $data->total(),
            'current_page' => $data->currentPage(),
            'per_page' => $data->perPage(),
            'last_page' => $data->lastPage(),
            'data' => $data->items(),
        ];
    }

    public static function fetchAndStore(Request $request)
    {
        $json = file_get_contents(storage_path('app/public/carriers.json'));
        $response = json_decode($json, true);

        if (! $response || ! isset($response['result']['data']['firstItem'])) {
            return collect([]);
        }

        $data = $response['result']['data']['firstItem'];
        $inserted = [];

        $carrier = self::updateOrCreate(
            ['dot_number' => $data['dot_number']],
            [
                'row_id' => uniqid(),
                'usdot_status' => $data['usdot_status'] ?? null,
                'dot_number' => $data['dot_number'] ?? null,
                'docket' => $data['docket'] ?? null,
                'docket_number' => $data['docket_number'] ?? null,
                'docket_prefix' => $data['docket_prefix'] ?? null,
                'legal_name' => ! empty($data['legal_name']) ? ucwords($data['legal_name']) : null,
                'dba_name' => $data['dba_name'] ?? null,
                'dba_flag' => $data['dba_flag'] ?? false,
                'duns' => $data['duns'] ?? null,
                'indicator_insurance' => $data['indicator_insurance'] ?? null,
                'mcs150_year' => $data['mcs150_year'] ?? null,
                'mcs150_mileage' => $data['mcs150_mileage'] ?? null,
                'company_contact_primary' => $data['company_contact_primary'] ?? null,
                'company_contact_secondary' => $data['company_contact_secondary'] ?? null,
                'telephone_number' => $data['telephone_number'] ?? null,
                'physical_telephone_number_authority' => $data['physical_telephone_number_authority'] ?? null,
                'fax_number' => $data['fax_number'] ?? null,
                'email_address' => $data['email_address'] ?? null,
                'email_domain' => $data['email_domain'] ?? null,
            ]
        );

        CarrierBasic::updateOrCreate(
            ['carrier_id' => $carrier->id],
            [
                'row_id' => uniqid(),
                'violations_oos_unsafe_driving' => $data['violations_oos_unsafe_driving'] ?? null,
                'basic_alert_unsafe_driving' => $data['basic_alert_unsafe_driving'] ?? false,
                'violations_vehicle_maintence' => $data['violations_vehicle_maintence'] ?? null,
                'violations_severe_vehicle_maintence' => $data['violations_severe_vehicle_maintence'] ?? null,
                'violations_oos_vehicle_maintence' => $data['violations_oos_vehicle_maintence'] ?? null,
                'basic_alert_vehicle_maintence' => $data['basic_alert_vehicle_maintence'] ?? false,
                'violations_controlled_substance' => $data['violations_controlled_substance'] ?? null,
                'violations_severe_controlled_substance' => $data['violations_severe_controlled_substance'] ?? null,
                'violations_oos_controlled_substance' => $data['violations_oos_controlled_substance'] ?? null,
                'basic_alert_controlled_substance' => $data['basic_alert_controlled_substance'] ?? false,
                'violations_driver_fitness' => $data['violations_driver_fitness'] ?? null,
                'violations_severe_driver_fitness' => $data['violations_severe_driver_fitness'] ?? null,
                'violations_oos_driver_fitness' => $data['violations_oos_driver_fitness'] ?? null,
                'basic_alert_driver_fitness' => $data['basic_alert_driver_fitness'] ?? false,
            ]
        );

        CarrierCrash::updateOrCreate(
            ['carrier_id' => $carrier->id],
            [
                'row_id' => uniqid(),
                'crash_fatalities' => $data['crash_fatalities'] ?? null,
                'crash_injuries' => $data['crash_injuries'] ?? null,
                'crashes_tow_away' => $data['crashes_tow_away'] ?? null,
                'crashes_total' => $data['crashes_total'] ?? null,
            ]
        );

        CarrierInspection::updateOrCreate(
            ['carrier_id' => $carrier->id],
            [
                'row_id' => uniqid(),
                'inspections_vehicle' => $data['inspections_vehicle'] ?? null,
                'inspections_vehicle_out_of_service' => $data['inspections_vehicle_out_of_service'] ?? null,
                'inspections_driver' => $data['inspections_driver'] ?? null,
                'inspections_driver_out_of_service' => $data['inspections_driver_out_of_service'] ?? null,
                'inspections_hazmat' => $data['inspections_hazmat'] ?? null,
                'inspections_hazmat_out_of_service' => $data['inspections_hazmat_out_of_service'] ?? null,
                'natl_avg_oos_vehicle' => $data['natl_avg_oos_vehicle'] ?? null,
                'natl_avg_oos_driver' => $data['natl_avg_oos_driver'] ?? null,
                'natl_avg_oos_hazmat' => $data['natl_avg_oos_hazmat'] ?? null,
            ]
        );

        $fleet = CarrierFleetSummary::updateOrCreate(
            ['carrier_id' => $carrier->id],
            [
                'row_id' => uniqid(),
                'total_power_units' => $data['total_power_units'] ?? null,
                'total_trucks' => $data['total_trucks'] ?? null,
                'total_trailers' => $data['total_trailers'] ?? null,
                'owned_trailers' => $data['owned_trailers'] ?? null,
                'inspected_power_units' => $data['inspected_power_units'] ?? null,
                'inspected_trailers' => $data['inspected_trailers'] ?? null,
                'observed_IM' => $data['observed_IM'] ?? null,
                'observed_IRPU' => $data['observed_IRPU'] ?? null,
                'observed_PUM' => $data['observed_PUM'] ?? null,
            ]
        );

        CarrierContactHistory::updateOrCreate(
            ['carrier_id' => $carrier->id],
            [
                'row_id' => uniqid(),
                'physical_address' => $data['physical_address'] ?? null,
                'physical_address_city' => $data['physical_address_city'] ?? null,
                'physical_address_state' => $data['physical_address_state'] ?? null,
                'physical_address_street' => $data['physical_address_street'] ?? null,
                'physical_address_zip_code' => $data['physical_address_zip_code'] ?? null,
                'physical_address_iso_country_code' => $data['physical_address_iso_country_code'] ?? null,
                'physical_address_authority' => $data['physical_address_iso_country_code'] ?? null,
                'undeliverable_physical_address' => $data['undeliverable_physical_address'] ?? false,
                'physical_address_authority_city' => $data['physical_address_iso_country_code'] ?? null,
                'physical_address_authority_state' => $data['physical_address_iso_country_code'] ?? null,
                'physical_address_authority_street' => $data['physical_address_iso_country_code'] ?? null,
                'physical_address_authority_zip_code' => $data['physical_address_iso_country_code'] ?? null,
                'mailing_address' => $data['mailing_address'] ?? null,
                'mailing_address_city' => $data['mailing_address_city'] ?? null,
                'mailing_address_state' => $data['mailing_address_state'] ?? null,
                'mailing_address_street' => $data['mailing_address_street'] ?? null,
                'mailing_address_zip_code' => $data['mailing_address_zip_code'] ?? null,
                'mailing_address_iso_country_code' => $data['mailing_address_iso_country_code'] ?? null,
                'undeliverable_mailing_address' => $data['undeliverable_mailing_address'] ?? false,
                'mailing_address_authority_city' => $data['undeliverable_mailing_address'] ?? false,
                'mailing_address_authority_state' => $data['undeliverable_mailing_address'] ?? false,
                'mailing_address_authority_street' => $data['undeliverable_mailing_address'] ?? false,
                'mailing_address_authority_zip_code' => $data['undeliverable_mailing_address'] ?? false,
            ]
        );

        CarrierCompanySnapshot::updateOrCreate(
            ['carrier_id' => $carrier->id],
            [
                'row_id' => uniqid(),
                'carrier_operation_desc' => $data['carrier_operation_desc'] ?? null,
                'authority_common' => $data['authority_common'] ?? null,
                'property' => $data['property'] ?? false,
                'passenger' => $data['passenger'] ?? false,
                'household_goods' => $data['household_goods'] ?? false,
                'household' => $data['household'] ?? false,
                'beverages' => $data['beverages'] ?? false,
                'building_materials' => $data['building_materials'] ?? false,
                'chemicals' => $data['chemicals'] ?? false,
                'coal_coke' => $data['coal_coke'] ?? false,
                'construction' => $data['construction'] ?? false,
                'dry_bulk_commodities' => $data['dry_bulk_commodities'] ?? false,
                'farm_supplies' => $data['farm_supplies'] ?? false,
                'fresh_produce' => $data['fresh_produce'] ?? false,
                'garbage_refuse_trash' => $data['garbage_refuse_trash'] ?? false,
                'general_freight' => $data['general_freight'] ?? false,
                'grain_feed_hay' => $data['grain_feed_hay'] ?? false,
                'intermodal_containers' => $data['intermodal_containers'] ?? false,
                'liquids_gases' => $data['liquids_gases'] ?? false,
                'livestock' => $data['livestock'] ?? false,
                'logs_poles_beams_lumber' => $data['logs_poles_beams_lumber'] ?? false,
                'machinery_large_objects' => $data['machinery_large_objects'] ?? false,
                'meat' => $data['meat'] ?? false,
                'metal_sheet_coils_rolls' => $data['metal_sheet_coils_rolls'] ?? false,
                'mobile_homes' => $data['mobile_homes'] ?? false,
                'motor_vehicles' => $data['motor_vehicles'] ?? false,
                'oilfield_equipment' => $data['oilfield_equipment'] ?? false,
                'other_cargo' => $data['other_cargo'] ?? false,
                'paper_products' => $data['paper_products'] ?? false,
                'passengers' => $data['passengers'] ?? false,
                'refrigerated_foods' => $data['refrigerated_foods'] ?? false,
                'us_mail' => $data['us_mail'] ?? false,
                'water_well' => $data['water_well'] ?? false,
                'operation_classification_desc' => $data['operation_classification_desc'] ?? null,
                'operation_class_authorized_for_hire' => $data['operation_class_authorized_for_hire'] ?? false,
                'operation_class_exempt_for_hire' => $data['operation_class_exempt_for_hire'] ?? false,
                'operation_class_private_property' => $data['operation_class_private_property'] ?? false,
                'operation_class_private_pass_business' => $data['operation_class_private_pass_business'] ?? false,
                'operation_class_private_pass_non_business' => $data['operation_class_private_pass_non_business'] ?? false,
                'operation_class_migrant' => $data['operation_class_migrant'] ?? false,
                'operation_class_us_mail' => $data['operation_class_us_mail'] ?? false,
                'operation_class_federal_govt' => $data['operation_class_federal_govt'] ?? false,
                'operation_class_state_govt' => $data['operation_class_state_govt'] ?? false,
                'operation_class_local_govt' => $data['operation_class_local_govt'] ?? false,
                'operation_class_indian_nation' => $data['operation_class_indian_nation'] ?? false,
            ]
        );

        $contactChange = CarrierContactChange::updateOrCreate(
            ['carrier_id' => $carrier->id],
            [
                'row_id' => uniqid(),
                'name_change_count' => $data['name_change_count'] ?? null,
                'name_last_changed' => $data['name_last_changed'] ?? null,
                'email_change_count' => $data['email_change_count'] ?? null,
                'email_last_changed' => $data['email_last_changed'] ?? null,
                'phone_change_count' => $data['phone_change_count'] ?? null,
                'address_change_count' => $data['address_change_count'] ?? null,
                'address_last_changed' => $data['address_last_changed'] ?? null,
                'contact_change_count' => $data['contact_change_count'] ?? null,
                'contact_last_changed' => $data['contact_last_changed'] ?? null,
            ]
        );

        if (isset($data['equipment_history']) && is_array($data['equipment_history'])) {
            foreach ($data['equipment_history'] as $equipment) {
                CarrierEquipmentHistory::create([
                    'row_id' => uniqid(),
                    'fleet_id' => $fleet->id,
                    'vin' => $equipment['vin'] ?? null,
                    'type' => $equipment['type'] ?? null,
                    'unit_type_desc' => $equipment['unit_type_desc'] ?? null,
                    'inspections_total_vin' => $equipment['inspections_total_vin'] ?? null,
                    'inspections_total_vin_dot' => $equipment['inspections_total_vin_dot'] ?? null,
                    'multi_dot_flag' => $equipment['multi_dot_flag'] ?? false,
                    'last_inspection_date_vin_dot' => $equipment['last_inspection_date_vin'] ?? null,
                    'last_inspected_under_dot' => $equipment['last_inspected_under_dot'] ?? null,
                    'most_recent_inspection_flag' => $equipment['most_recent_inspection_flag'] ?? null,
                ]);
            }
        }

        $insurance = CarrierInsurance::updateOrCreate(
            ['carrier_id' => $carrier->id],
            [
                'row_id' => uniqid(),
                'insurance_cancel_count' => $data['insurance_cancel_count'] ?? null,
                'insurance_last_canceled' => ! empty($data['insurance_last_canceled'])
                    ? date('d-m-Y', strtotime($data['insurance_last_canceled']))
                    : null,
                'insurance_bipd_on_file' => $data['insurance_bipd_on_file'] ?? null,
                'insurance_bipd_required' => $data['insurance_bipd_required'] ?? false,
                'insurance_bond_on_file' => $data['insurance_bond_on_file'] ?? null,
                'insurance_bond_required' => $data['insurance_bond_required'] ?? false,
                'insurance_cargo_on_file' => $data['insurance_cargo_on_file'] ?? null,
                'insurance_cargo_required' => $data['insurance_cargo_required'] ?? false,
            ]
        );

        if (isset($data['insurance_history']) && is_array($data['insurance_history'])) {
            foreach ($data['insurance_history'] as $insuranceItem) {
                CarrierInsuranceHistory::create([
                    'row_id' => uniqid(),
                    'insurance_id' => $insurance->id,
                    'insurance_status' => $insuranceItem['insurance_status'] ?? null,
                    'insurance_form_code' => $insuranceItem['insurance_form_code'] ?? null,
                    'insurance_type_code' => $insuranceItem['insurance_type_code'] ?? null,
                    'insurance_carrier' => $insuranceItem['insurance_carrier'] ?? null,
                    'policy_number' => $insuranceItem['policy_number'] ?? null,
                    'effective_date' => $insuranceItem['effective_date'] ?? null,
                    'cancel_method' => $insuranceItem['cancel_method'] ?? null,
                    'cancel_effective_date' => $insuranceItem['cancel_effective_date'] ?? null,
                    'underlying_limit_amount' => $insuranceItem['underlying_limit_amount'] ?? null,
                    'minimum_coverage_amount' => $insuranceItem['minimum_coverage_amount'] ?? null,
                ]);
            }
        }

        if (isset($data['contact_history']) && is_array($data['contact_history'])) {
            foreach ($data['contact_history'] as $contactItem) {
                CarrierContactHistoryLog::create([
                    'row_id' => uniqid(),
                    'contact_change_id' => $contactChange->id,
                    'history_key' => $contactItem['key'] ?? null,
                    'history_value' => $contactItem['value'] ?? null,
                    'start_date' => $contactItem['start_date'] ?? null,
                    'end_date' => $contactItem['end_date'] ?? null,
                    'current_flag' => $contactItem['current_flag'] ?? false,
                ]);
            }
        }

        if (isset($data['address_ids']) && is_array($data['address_ids'])) {
            foreach ($data['address_ids'] as $addressItem) {
                CarrierAddressId::create([
                    'row_id' => uniqid(),
                    'contact_change_id' => $contactChange->id,
                    'address_key' => $addressItem['address_key'] ?? null,
                    'address_value' => $addressItem['address_value'] ?? null,
                    'start_date' => $addressItem['start_date'] ?? null,
                    'end_date' => $addressItem['end_date'] ?? null,
                    'current_flag' => $addressItem['current_flag'] ?? false,
                ]);
            }
        }

        $inserted[] = $carrier;

        return collect($inserted);
    }

    public static function detail(Request $request)
    {
        $rowId = trim($request->query('row_id', ''));

        if (empty($rowId)) {
            return [
                'success' => false,
                'message' => 'row_id is required',
            ];
        }

        $carrier = self::where('row_id', $rowId)
            ->select([
                'id',
                'row_id',
                'dot_number',
                'docket_number',
                'docket',
                'docket_prefix',
                'legal_name',
                'dba_name',
                'entity_type_desc',
                'usdot_status',
                'telephone_number',
                'fax_number',
                'email_address',
                'email_domain',
                'ein',
                'duns',
                'mcs150_mileage',
                'mcs150_year',
                'risk_score',
                'authority_contract',
                'authority_broker',
                'indicator_insurance',
                'indicator_authority',
                'added_date',
                'safety_rating_desc',
                'safety_rating_date',
            ])
            ->first();

        if (! $carrier) {
            return [
                'success' => false,
                'message' => 'Carrier not found',
            ];
        }

        return [
            'success' => true,
            'data' => [
                'id' => $carrier->id,
                'row_id' => $carrier->row_id,
                'dot_number' => $carrier->dot_number,
                'mc_number' => $carrier->docket_number,
                'docket' => $carrier->docket,
                'docket_prefix' => $carrier->docket_prefix,
                'company_name' => $carrier->legal_name,
                'dba_name' => $carrier->dba_name,
                'carrier_type' => $carrier->entity_type_desc,
                'usdot_status' => $carrier->usdot_status,
                'phone' => $carrier->telephone_number,
                'fax' => $carrier->fax_number,
                'email' => $carrier->email_address,
                'email_domain' => $carrier->email_domain,
                'ein' => $carrier->ein,
                'duns' => $carrier->duns,
                'mileage' => $carrier->mcs150_mileage,
                'mcs150_year' => $carrier->mcs150_year,
                'risk_level' => $carrier->risk_score,
                'authority_verified' => $carrier->authority_contract === 'Active',
                'insurance_current' => (bool) $carrier->indicator_insurance,
                'authority_active' => (bool) $carrier->indicator_authority,
                'safety_rating' => $carrier->safety_rating_desc,
                'safety_rating_date' => $carrier->safety_rating_date,
                'added_date' => $carrier->added_date,
            ],
        ];
    }

    public function basics(): HasMany
    {
        return $this->hasMany(CarrierBasic::class, 'carrier_id', 'id');
    }

    public function crashes(): HasMany
    {
        return $this->hasMany(CarrierCrash::class, 'carrier_id', 'id');
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(CarrierInspection::class, 'carrier_id', 'id');
    }

    public function fleetSummaries(): HasMany
    {
        return $this->hasMany(CarrierFleetSummary::class, 'carrier_id', 'id');
    }

    public function insurances(): HasMany
    {
        return $this->hasMany(CarrierInsurance::class, 'carrier_id', 'id');
    }

    public function contactChanges(): HasMany
    {
        return $this->hasMany(CarrierContactChange::class, 'carrier_id', 'id');
    }

    public function contactHistories(): HasMany
    {
        return $this->hasMany(CarrierContactHistory::class, 'carrier_id', 'id');
    }

    public function companySnapshots(): HasMany
    {
        return $this->hasMany(CarrierCompanySnapshot::class, 'carrier_id', 'id');
    }

    public function syncLogs()
    {
        return $this->hasMany(CarrierSyncLog::class);
    }

    public function changeLogs()
    {
        return $this->hasMany(CarrierChangeLog::class);
    }

    public function syncState()
    {
        return $this->hasOne(CarrierSyncState::class);
    }

    public function apiSnapshots()
    {
        return $this->hasMany(CarrierApiSnapshot::class);
    }
}
