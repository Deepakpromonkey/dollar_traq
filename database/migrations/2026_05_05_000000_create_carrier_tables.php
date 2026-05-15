<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carriers', function (Blueprint $table) {
            $table->id();
            $table->string('row_id', 50)->nullable();
            $table->string('usdot_status', 50)->nullable();
            $table->bigInteger('dot_number')->nullable();
            $table->string('docket', 50)->nullable();
            $table->bigInteger('docket_number')->nullable();
            $table->bigInteger('ein')->nullable();
            $table->string('docket_prefix', 10)->nullable();
            $table->string('legal_name', 255)->nullable();
            $table->string('dba_name', 255)->nullable();
            $table->boolean('dba_flag')->nullable();
            $table->bigInteger('duns')->nullable();
            $table->integer('mcs150_year')->nullable();
            $table->bigInteger('mcs150_mileage')->nullable();
            $table->string('company_contact_primary', 255)->nullable();
            $table->string('company_contact_secondary', 255)->nullable();
            $table->string('telephone_number')->nullable();
            $table->string('fax_number')->nullable();
            $table->string('cellphone_number')->nullable();
            $table->string('physical_telephone_number_authority')->nullable();
            $table->string('mailing_telephone_number_authority')->nullable();

            $table->string('email_address', 255)->nullable();
            $table->string('email_domain', 255)->nullable();
            $table->string('entity_type_desc')->nullable();
            $table->string('authority_contract')->nullable();
            $table->string('authority_broker')->nullable();

            $table->boolean('authority_common_pending')->nullable();
            $table->boolean('authority_contract_pending')->nullable();
            $table->boolean('authority_broker_pending')->nullable();

            $table->boolean('authority_common_review')->nullable();
            $table->boolean('authority_contract_review')->nullable();
            $table->boolean('authority_broker_review')->nullable();

            $table->boolean('authority_common_revocation')->nullable();
            $table->boolean('authority_contract_revocation')->nullable();
            $table->boolean('authority_broker_revocation')->nullable();

            $table->string('authority_age_contract')->nullable();
            $table->string('authority_age_contract_active')->nullable();
            $table->string('authority_age_broker')->nullable();
            $table->string('authority_age_broker_active')->nullable();

            $table->integer('total_revocations')->nullable();
            $table->integer('days_since_last_revocation')->nullable();

            $table->boolean('indicator_authority')->nullable();
            $table->boolean('indicator_insurance')->nullable();

            $table->boolean('private')->nullable();
            $table->boolean('enterprise')->nullable();

            $table->string('latest_review_type_desc')->nullable();
            $table->string('safety_rating_desc')->nullable();

            $table->integer('total_drivers')->nullable();

            $table->string('organization_type_desc')->nullable();

            $table->string('icc_dockets')->nullable();

            $table->integer('owned_trucks')->nullable();
            $table->integer('owned_tractors')->nullable();
            $table->integer('total_buses')->nullable();

            $table->integer('num_drivers_interstate_beyond_100mi')->nullable();
            $table->integer('num_drivers_interstate_total')->nullable();
            $table->integer('num_drivers_intrastate_total')->nullable();
            $table->integer('avg_trip_leased_drivers')->nullable();
            $table->integer('total_drivers_cdl')->nullable();

            $table->string('mcs151_type_desc')->nullable();

            $table->integer('dot_age')->nullable();

            $table->string('boc3_company_name')->nullable();
            $table->string('boc3_attn')->nullable();

            $table->string('bo3_address_city')->nullable();
            $table->string('boc3_address_state')->nullable();
            $table->string('boc3_address_street')->nullable();
            $table->string('boc3_address_zip_code')->nullable();
            $table->string('boc3_address_country_code')->nullable();

            $table->boolean('out_of_service_flag')->nullable();
            $table->boolean('hazardous_material')->nullable();

            $table->string('physical_address_id')->nullable();
            $table->string('mailing_address_id')->nullable();
            $table->string('physical_address_authority_id')->nullable();
            $table->string('mailing_address_authority_id')->nullable();

            $table->date('mcs150_date')->nullable();
            $table->date('mcs151_date')->nullable();
            $table->integer('mcs151_year')->nullable();

            $table->date('added_date')->nullable();

            $table->date('last_revocation_date')->nullable();
            $table->date('last_violation_date')->nullable();
            $table->date('last_crash_date')->nullable();
            $table->date('last_inspection_date')->nullable();
            $table->date('latest_review_date')->nullable();

            $table->date('safety_rating_date')->nullable();
            $table->date('snapshot_date')->nullable();

            $table->boolean('smartway_flag')->nullable();
            $table->boolean('carbtru_flag')->nullable();
            $table->boolean('phmsa_flag')->nullable();

            $table->string('risk_score')->nullable();
            $table->float('risk_score_probability')->nullable();

            $table->integer('iss_value')->nullable();
            $table->string('iss_recommendation')->nullable();
            $table->text('iss_recommendation_reason')->nullable();

            $table->string('safety_score')->nullable();

            $table->boolean('indicator_carrier_safety')->nullable();
            $table->boolean('indicator_industry_benchmarks')->nullable();

            $table->boolean('indicator_benchmark_inspection_mileage_ratio')->nullable();
            $table->boolean('indicator_benchmark_inspected_power_units_ratio')->nullable();
            $table->boolean('indicator_benchmark_power_unit_mileage_ratio')->nullable();

            $table->boolean('indicator_network_graph_contact')->nullable();
            $table->boolean('indicator_network_graph_equipment')->nullable();
            $table->integer('total_loads')->nullable();

            $table->integer('ltl_loads')->nullable();
            $table->decimal('ltl_percentage', 10, 4)->nullable();

            $table->integer('ftl_loads')->nullable();
            $table->decimal('ftl_percentage', 10, 4)->nullable();

            $table->integer('deadheads')->nullable();
            $table->decimal('deadhead_percentage', 10, 4)->nullable();

            $table->date('first_load_date')->nullable();
            $table->date('last_load_date')->nullable();
            $table->timestamps();
        });

        Schema::create('carrier_basics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('carrier_id')->nullable();
            $table->string('row_id', 50)->nullable();
            $table->bigInteger('violations_oos_unsafe_driving')->nullable();
            $table->boolean('basic_alert_unsafe_driving')->nullable();
            $table->bigInteger('violations_vehicle_maintence')->nullable();
            $table->bigInteger('violations_severe_vehicle_maintence')->nullable();
            $table->bigInteger('violations_oos_vehicle_maintence')->nullable();
            $table->boolean('basic_alert_vehicle_maintence')->nullable();
            $table->bigInteger('violations_controlled_substance')->nullable();
            $table->bigInteger('violations_severe_controlled_substance')->nullable();
            $table->bigInteger('violations_oos_controlled_substance')->nullable();
            $table->boolean('basic_alert_controlled_substance')->nullable();
            $table->bigInteger('violations_driver_fitness')->nullable();
            $table->bigInteger('violations_severe_driver_fitness')->nullable();
            $table->bigInteger('violations_oos_driver_fitness')->nullable();
            $table->boolean('basic_alert_driver_fitness')->nullable();
            $table->integer('violations_unsafe_driving')->nullable();
            $table->integer('violations_severe_unsafe_driving')->nullable();

            $table->float('basic_percentile_unsafe_driving')->nullable();

            $table->boolean('basic_roadside_alert_unsafe_driving')->nullable();
            $table->boolean('basic_ac_indicator_unsafe_driving')->nullable();

            $table->float('intervention_threshold_unsafe_driving')->nullable();

            $table->integer('violations_hours_of_service')->nullable();
            $table->integer('violations_severe_hours_of_service')->nullable();
            $table->integer('violations_oos_hours_of_service')->nullable();

            $table->boolean('basic_alert_hours_of_service')->nullable();
            $table->boolean('basic_roadside_alert_hours_of_service')->nullable();
            $table->boolean('basic_ac_indicator_hours_of_service')->nullable();

            $table->float('intervention_threshold_hours_of_service')->nullable();

            $table->boolean('basic_roadside_alert_vehicle_maintence')->nullable();
            $table->boolean('basic_ac_indicator_vehicle_maintence')->nullable();

            $table->float('intervention_threshold_vehicle_maintence')->nullable();

            $table->boolean('basic_roadside_alert_controlled_substance')->nullable();
            $table->boolean('basic_ac_indicator_controlled_substance')->nullable();

            $table->float('intervention_threshold_controlled_substance')->nullable();

            $table->boolean('basic_roadside_alert_driver_fitness')->nullable();
            $table->boolean('basic_ac_indicator_driver_fitness')->nullable();

            $table->float('intervention_threshold_driver_fitness')->nullable();

            $table->integer('violations_hazardous_materials')->nullable();
            $table->integer('violations_severe_hazardous_materials')->nullable();
            $table->integer('violations_oos_hazardous_materials')->nullable();

            $table->float('basic_measure_hazardous_materials')->nullable();

            $table->boolean('basic_alert_hazardous_materials')->nullable();
            $table->boolean('basic_roadside_alert_hazardous_materials')->nullable();
            $table->boolean('basic_ac_indicator_hazardous_materials')->nullable();

            $table->float('intervention_threshold_hazardous_materials')->nullable();

            $table->float('basic_measure_crash_indicator')->nullable();

            $table->boolean('basic_alert_crash_indicator')->nullable();
            $table->boolean('basic_roadside_alert_crash_indicator')->nullable();
            $table->boolean('basic_ac_indicator_crash_indicator')->nullable();

            $table->float('intervention_threshold_crash_indicator')->nullable();

            $table->float('basic_measure_unsafe_driving')->nullable();
            $table->float('basic_measure_hours_of_service')->nullable();
            $table->float('basic_measure_vehicle_maintence')->nullable();
            $table->float('basic_measure_controlled_substance')->nullable();
            $table->float('basic_measure_driver_fitness')->nullable();
            $table->timestamps();
            $table->foreign('carrier_id')->references('id')->on('carriers')->onDelete('cascade');
        });

        Schema::create('carrier_crashes', function (Blueprint $table) {
            $table->id();
            $table->string('row_id', 50)->nullable();
            $table->unsignedBigInteger('carrier_id')->nullable();
            $table->bigInteger('crash_fatalities')->nullable();
            $table->bigInteger('crash_injuries')->nullable();
            $table->bigInteger('crashes_tow_away')->nullable();
            $table->bigInteger('crashes_total')->nullable();
            $table->timestamps();
            $table->foreign('carrier_id')->references('id')->on('carriers')->onDelete('cascade');
        });

        Schema::create('carrier_inspections', function (Blueprint $table) {
            $table->id();
            $table->string('row_id', 50)->nullable();
            $table->unsignedBigInteger('carrier_id')->nullable();
            $table->bigInteger('inspections_vehicle')->nullable();
            $table->bigInteger('inspections_vehicle_out_of_service')->nullable();
            $table->bigInteger('inspections_driver')->nullable();
            $table->bigInteger('inspections_driver_out_of_service')->nullable();
            $table->bigInteger('inspections_hazmat')->nullable();
            $table->bigInteger('inspections_hazmat_out_of_service')->nullable();
            $table->string('natl_avg_oos_vehicle', 20)->nullable();
            $table->string('natl_avg_oos_driver', 20)->nullable();
            $table->string('natl_avg_oos_hazmat', 20)->nullable();
            $table->boolean('oos_alert_driver')->nullable();
            $table->boolean('oos_alert_hazmat')->nullable();
            $table->boolean('oos_alert_vehicle')->nullable();

            $table->integer('violations_total')->nullable();

            $table->integer('inspections_total')->nullable();
            $table->integer('inspected_states')->nullable();

            $table->float('inspections_vehicle_out_of_service_pct')->nullable();
            $table->float('inspections_driver_out_of_service_pct')->nullable();
            $table->float('inspections_hazmat_out_of_service_pct')->nullable();
            $table->timestamps();
            $table->foreign('carrier_id')->references('id')->on('carriers')->onDelete('cascade');
        });

        Schema::create('carrier_fleet_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('row_id', 50)->nullable();
            $table->unsignedBigInteger('carrier_id')->nullable();
            $table->bigInteger('total_power_units')->nullable();
            $table->bigInteger('total_trucks')->nullable();
            $table->bigInteger('total_trailers')->nullable();
            $table->bigInteger('owned_trailers')->nullable();
            $table->bigInteger('inspected_power_units')->nullable();
            $table->bigInteger('inspected_trailers')->nullable();
            $table->bigInteger('observed_IM')->nullable();
            $table->bigInteger('observed_IRPU')->nullable();
            $table->bigInteger('observed_PUM')->nullable();
            $table->integer('expected_lb_IM')->nullable();
            $table->integer('expected_ub_IM')->nullable();

            $table->integer('expected_lb_IRPU')->nullable();
            $table->integer('expected_ub_IRPU')->nullable();

            $table->integer('expected_lb_PUM')->nullable();
            $table->integer('expected_ub_PUM')->nullable();
            $table->timestamps();
            $table->foreign('carrier_id')->references('id')->on('carriers')->onDelete('cascade');
        });

        Schema::create('carrier_insurances', function (Blueprint $table) {
            $table->id();
            $table->string('row_id', 50)->nullable();
            $table->unsignedBigInteger('carrier_id')->nullable();
            $table->bigInteger('insurance_cancel_count')->nullable();
            $table->string('insurance_last_canceled', 50)->nullable();
            $table->bigInteger('insurance_bipd_on_file')->nullable();

            $table->bigInteger('insurance_bond_on_file')->nullable();

            $table->string('insurance_cargo_on_file')->nullable();
            $table->bigInteger('insurance_bipd_required')->nullable();
            $table->bigInteger('insurance_bond_required')->nullable();
            $table->bigInteger('insurance_cargo_required')->nullable();
            $table->timestamps();
            $table->foreign('carrier_id')->references('id')->on('carriers')->onDelete('cascade');
        });

        Schema::create('carrier_contact_changes', function (Blueprint $table) {
            $table->id();
            $table->string('row_id', 50)->nullable();
            $table->unsignedBigInteger('carrier_id')->nullable();
            $table->bigInteger('name_change_count')->nullable();
            $table->string('name_last_changed', 50)->nullable();
            $table->bigInteger('email_change_count')->nullable();
            $table->string('email_last_changed', 50)->nullable();
            $table->bigInteger('phone_change_count')->nullable();
            $table->string('phone_last_changed', 50)->nullable();
            $table->bigInteger('address_change_count')->nullable();
            $table->string('address_last_changed', 50)->nullable();
            $table->bigInteger('contact_change_count')->nullable();
            $table->string('contact_last_changed', 50)->nullable();
            $table->timestamps();
            $table->foreign('carrier_id')->references('id')->on('carriers')->onDelete('cascade');
        });

        Schema::create('carrier_contact_histories', function (Blueprint $table) {
            $table->id();
            $table->string('row_id', 50)->nullable();
            $table->unsignedBigInteger('carrier_id')->nullable();
            $table->text('physical_address')->nullable();
            $table->string('physical_address_city', 100)->nullable();
            $table->string('physical_address_state', 100)->nullable();
            $table->string('physical_address_street', 255)->nullable();
            $table->string('physical_address_zip_code', 20)->nullable();
            $table->string('physical_address_iso_country_code', 10)->nullable();
            $table->boolean('undeliverable_physical_address')->nullable();
            $table->text('physical_address_authority')->nullable();
            $table->string('physical_address_authority_city', 100)->nullable();
            $table->string('physical_address_authority_state', 100)->nullable();
            $table->string('physical_address_authority_street', 255)->nullable();
            $table->string('physical_address_authority_zip_code', 20)->nullable();
            $table->text('mailing_address')->nullable();
            $table->string('mailing_address_city', 100)->nullable();
            $table->string('mailing_address_state', 100)->nullable();
            $table->string('mailing_address_street', 255)->nullable();
            $table->string('mailing_address_zip_code', 20)->nullable();
            $table->string('mailing_address_iso_country_code', 10)->nullable();
            $table->boolean('undeliverable_mailing_address')->nullable();
            $table->text('mailing_address_authority')->nullable();
            $table->string('mailing_address_authority_city', 100)->nullable();
            $table->string('mailing_address_authority_state', 100)->nullable();
            $table->string('mailing_address_authority_street', 255)->nullable();
            $table->string('mailing_address_authority_zip_code', 20)->nullable();
            $table->boolean('undeliverable_physical_address_authority')->nullable();
            $table->timestamps();
            $table->foreign('carrier_id')->references('id')->on('carriers')->onDelete('cascade');
        });

        Schema::create('carrier_company_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('row_id', 50)->nullable();
            $table->unsignedBigInteger('carrier_id')->nullable();
            $table->text('carrier_operation_desc')->nullable();
            $table->string('authority_common', 100)->nullable();
            $table->boolean('property')->nullable();
            $table->boolean('passenger')->nullable();
            $table->boolean('household_goods')->nullable();
            $table->boolean('household')->nullable();
            $table->text('cargo_carried')->nullable();
            $table->boolean('beverages')->nullable();
            $table->boolean('building_materials')->nullable();
            $table->boolean('chemicals')->nullable();
            $table->boolean('coal_coke')->nullable();
            $table->boolean('construction')->nullable();
            $table->boolean('dry_bulk_commodities')->nullable();
            $table->boolean('farm_supplies')->nullable();
            $table->boolean('fresh_produce')->nullable();
            $table->boolean('garbage_refuse_trash')->nullable();
            $table->boolean('general_freight')->nullable();
            $table->boolean('grain_feed_hay')->nullable();
            $table->boolean('intermodal_containers')->nullable();
            $table->boolean('liquids_gases')->nullable();
            $table->boolean('livestock')->nullable();
            $table->boolean('logs_poles_beams_lumber')->nullable();
            $table->boolean('machinery_large_objects')->nullable();
            $table->boolean('meat')->nullable();
            $table->boolean('metal_sheet_coils_rolls')->nullable();
            $table->boolean('mobile_homes')->nullable();
            $table->boolean('motor_vehicles')->nullable();
            $table->boolean('oilfield_equipment')->nullable();
            $table->boolean('other_cargo')->nullable();
            $table->boolean('paper_products')->nullable();
            $table->boolean('passengers')->nullable();
            $table->boolean('refrigerated_foods')->nullable();
            $table->boolean('us_mail')->nullable();
            $table->boolean('water_well')->nullable();
            $table->text('operation_classification_desc')->nullable();
            $table->boolean('operation_class_authorized_for_hire')->nullable();
            $table->boolean('operation_class_exempt_for_hire')->nullable();
            $table->boolean('operation_class_private_property')->nullable();
            $table->boolean('operation_class_private_pass_business')->nullable();
            $table->boolean('operation_class_private_pass_non_business')->nullable();
            $table->boolean('operation_class_migrant')->nullable();
            $table->boolean('operation_class_us_mail')->nullable();
            $table->boolean('operation_class_federal_govt')->nullable();
            $table->boolean('operation_class_state_govt')->nullable();
            $table->boolean('operation_class_local_govt')->nullable();
            $table->boolean('operation_class_indian_nation')->nullable();
            $table->string('authority_start_contract', 50)->nullable();
            $table->string('authority_start_broker', 50)->nullable();
            $table->string('authority_common_last_revocation_date', 50)->nullable();
            $table->string('authority_contract_last_revocation_date', 50)->nullable();
            $table->string('authority_broker_last_revocation_date', 50)->nullable();
            $table->string('authority_contract')->nullable();
            $table->string('authority_broker')->nullable();
            $table->boolean('driveaway_towaway')->nullable();
            $table->boolean('utility')->nullable();
            $table->boolean('authority_common_pending')->nullable();
            $table->boolean('authority_contract_pending')->nullable();
            $table->boolean('authority_broker_pending')->nullable();
            $table->timestamps();
            $table->foreign('carrier_id')->references('id')->on('carriers')->onDelete('cascade');
        });

        Schema::create('carrier_equipment_histories', function (Blueprint $table) {
            $table->id();
            $table->string('row_id', 50)->nullable();
            $table->unsignedBigInteger('fleet_id')->nullable();
            $table->string('vin', 100)->nullable();
            $table->string('type', 50)->nullable();
            $table->string('unit_type_desc', 255)->nullable();
            $table->bigInteger('inspections_total_vin')->nullable();
            $table->bigInteger('inspections_total_vin_dot')->nullable();
            $table->bigInteger('countd_dots_vin')->nullable();
            $table->boolean('multi_dot_flag')->nullable();
            $table->string('last_inspection_date_vin', 50)->nullable();
            $table->string('last_inspection_date_vin_dot', 50)->nullable();
            $table->bigInteger('last_inspected_under_dot')->nullable();
            $table->boolean('most_recent_inspection_flag')->nullable();
            $table->string('primary_category')->nullable();
            $table->string('secondary_category')->nullable();

            $table->string('insp_unit_license')->nullable();
            $table->string('insp_unit_license_state')->nullable();
            $table->string('insp_unit_company')->nullable();

            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('model_year')->nullable();

            $table->string('vehicle_type')->nullable();
            $table->string('gvwr')->nullable();

            $table->string('body_class')->nullable();
            $table->string('ncsa_body_type')->nullable();

            $table->string('fuel_type_primary')->nullable();
            $table->string('body_cab_type')->nullable();

            $table->text('other_trailer_info')->nullable();

            $table->string('trailer_body_type')->nullable();
            $table->string('trailer_type')->nullable();

            $table->integer('trailer_length')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('fleet_id')->references('id')->on('carrier_fleet_summaries')->onDelete('cascade');
        });

        Schema::create('carrier_insurance_histories', function (Blueprint $table) {
            $table->id();
            $table->string('row_id', 50)->nullable();
            $table->unsignedBigInteger('insurance_id')->nullable();
            $table->string('insurance_status', 50)->nullable();
            $table->string('insurance_form_code', 50)->nullable();
            $table->string('insurance_type_code', 50)->nullable();
            $table->string('insurance_carrier', 255)->nullable();
            $table->string('policy_number', 100)->nullable();
            $table->string('effective_date', 50)->nullable();
            $table->string('cancel_method', 100)->nullable();
            $table->string('cancel_effective_date', 50)->nullable();
            $table->bigInteger('underlying_limit_amount')->nullable();
            $table->bigInteger('minimum_coverage_amount')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('insurance_id')->references('id')->on('carrier_insurances')->onDelete('cascade');
        });

        Schema::create('carrier_contact_history_logs', function (Blueprint $table) {
            $table->id();
            $table->string('row_id', 50)->nullable();
            $table->unsignedBigInteger('contact_change_id')->nullable();
            $table->string('history_key', 100)->nullable();
            $table->text('history_value')->nullable();
            $table->string('start_date', 50)->nullable();
            $table->string('end_date', 50)->nullable();
            $table->boolean('current_flag')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('contact_change_id')->references('id')->on('carrier_contact_changes')->onDelete('cascade');
        });

        Schema::create('carrier_address_ids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('carrier_id');
            $table->foreign('carrier_id')
                ->references('id')
                ->on('carriers')
                ->onDelete('cascade');
            $table->string('row_id', 50)->nullable();
            $table->unsignedBigInteger('contact_change_id')->nullable();
            $table->string('address_key', 100)->nullable();
            $table->string('address_value', 255)->nullable();
            $table->string('start_date', 50)->nullable();
            $table->string('end_date', 50)->nullable();
            $table->boolean('current_flag')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('contact_change_id')->references('id')->on('carrier_contact_changes')->onDelete('cascade');
        });
        Schema::create('carrier_authority_histories', function (Blueprint $table) {
            $table->id();

            $table->string('row_id', 50)->nullable();
            $table->unsignedBigInteger('carrier_id');
            $table->foreign('carrier_id')
                ->references('id')
                ->on('carriers')
                ->onDelete('cascade');
            $table->string('authority_type_desc')->nullable();
            $table->string('original_action_desc')->nullable();
            $table->date('original_served_date')->nullable();

            $table->string('dispensation_action_desc')->nullable();
            $table->date('dispensation_served_date')->nullable();

            $table->timestamps();
        });
        Schema::create('carrier_geo_points', function (Blueprint $table) {
            $table->id();
            $table->string('row_id', 50)->nullable();

            $table->unsignedBigInteger('carrier_id');
            $table->foreign('carrier_id')
                ->references('id')
                ->on('carriers')
                ->onDelete('cascade');
            $table->json('geo_data')->nullable();

            $table->timestamps();
        });
        Schema::create('carrier_risk_factors', function (Blueprint $table) {
            $table->id();
            $table->string('row_id', 50)->nullable();

            $table->unsignedBigInteger('carrier_id');
            $table->foreign('carrier_id')
                ->references('id')
                ->on('carriers')
                ->onDelete('cascade');
            $table->json('risk_data')->nullable();
            $table->boolean('consecutive_authority')->nullable();
            $table->boolean('sixty_mo_in_business')->nullable();

            $table->boolean('revocation_last_thirtysix_mo')->nullable();

            $table->boolean('indicator_network_graph_phone')->nullable();
            $table->boolean('indicator_network_graph_address')->nullable();
            $table->boolean('indicator_network_graph_email')->nullable();
            $table->boolean('indicator_network_graph_ein')->nullable();
            $table->boolean('indicator_network_graph_duns')->nullable();

            $table->boolean('high_shared_power_units')->nullable();

            $table->boolean('zeo_inspections_last_twelve_mo')->nullable();

            $table->boolean('active_usdot_status')->nullable();

            $table->boolean('violations_severe_flag')->nullable();

            $table->boolean('basic_alert_flag')->nullable();
            $table->boolean('basic_ac_indicator_flag')->nullable();

            $table->boolean('fatal_crashes_flag')->nullable();

            $table->boolean('safety_rating_unsatisfactory_conditional')->nullable();

            $table->boolean('secondary_contact_info_provided')->nullable();
            $table->boolean('primary_contact_info_missing')->nullable();

            $table->boolean('carrier_w_brokerage_authority')->nullable();

            $table->boolean('multi_cargo_classification')->nullable();

            $table->boolean('interstate_carrier_single_state_inspection')->nullable();

            $table->boolean('bipd_insurance_above_minimum')->nullable();
            $table->boolean('bipd_insurance_below_requirement')->nullable();

            $table->boolean('bond_insurance_below_requirement')->nullable();

            $table->boolean('cargo_insurance_on_file')->nullable();

            $table->boolean('boc3_on_file')->nullable();

            $table->boolean('pending_insurance_cancellation')->nullable();

            $table->boolean('ins_rrg_active')->nullable();
            $table->boolean('ins_rrg_hist')->nullable();

            $table->boolean('no_active_authority')->nullable();

            $table->boolean('not_authorized_for_hire')->nullable();

            $table->boolean('mcs150_filed_last_24_months')->nullable();

            $table->boolean('oos_below_industry_average')->nullable();

            $table->boolean('virtual_physical_mailing_address')->nullable();
            $table->timestamps();
        });
        Schema::create('carrier_load_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('row_id', 50)->nullable();

            $table->unsignedBigInteger('carrier_id');
            $table->foreign('carrier_id')
                ->references('id')
                ->on('carriers')
                ->onDelete('cascade');
            $table->string('load_key')->nullable();
            $table->string('load_value')->nullable();

            $table->date('first_load_date')->nullable();
            $table->date('last_load_date')->nullable();

            $table->timestamps();
        });
        Schema::create('carrier_preferred_lanes', function (Blueprint $table) {
            $table->id();
            $table->string('row_id', 50)->nullable();

            $table->unsignedBigInteger('carrier_id');
            $table->foreign('carrier_id')
                ->references('id')
                ->on('carriers')
                ->onDelete('cascade');
            $table->string('state')->nullable();
            $table->integer('inspections')->nullable();

            $table->timestamps();
        });
        Schema::create('carrier_network_graphs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('carrier_id');

            $table->string('graph_type')->nullable();
            $table->json('graph_data')->nullable();

            $table->timestamps();

            $table->foreign('carrier_id')
                ->references('id')
                ->on('carriers')
                ->onDelete('cascade');
        });
        Schema::create('carrier_equipment_summaries', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('carrier_id');

            $table->string('summary_type')->nullable();
            $table->string('equipment_type')->nullable();

            $table->string('category')->nullable();

            $table->integer('count')->nullable();

            $table->timestamps();

            $table->foreign('carrier_id')
                ->references('id')
                ->on('carriers')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carrier_preferred_lanes');
        Schema::dropIfExists('carrier_load_summaries');
        Schema::dropIfExists('carrier_risk_factors');
        Schema::dropIfExists('carrier_geo_points');
        Schema::dropIfExists('carrier_authority_histories');

        Schema::dropIfExists('carrier_address_ids');
        Schema::dropIfExists('carrier_contact_history_logs');
        Schema::dropIfExists('carrier_insurance_histories');
        Schema::dropIfExists('carrier_equipment_histories');

        Schema::dropIfExists('carrier_company_snapshots');
        Schema::dropIfExists('carrier_contact_histories');
        Schema::dropIfExists('carrier_contact_changes');
        Schema::dropIfExists('carrier_insurances');
        Schema::dropIfExists('carrier_fleet_summaries');
        Schema::dropIfExists('carrier_inspections');
        Schema::dropIfExists('carrier_crashes');
        Schema::dropIfExists('carrier_basics');

        Schema::dropIfExists('carriers');
    }
};
