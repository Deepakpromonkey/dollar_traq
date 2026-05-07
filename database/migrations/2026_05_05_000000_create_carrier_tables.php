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
            $table->bigInteger('telephone_number')->nullable();
            $table->bigInteger('physical_telephone_number_authority')->nullable();
            $table->bigInteger('mailing_telephone_number_authority')->nullable();
            $table->bigInteger('cellphone_number')->nullable();
            $table->bigInteger('fax_number')->nullable();
            $table->string('email_address', 255)->nullable();
            $table->string('email_domain', 255)->nullable();
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
            $table->boolean('insurance_bipd_required')->nullable();
            $table->bigInteger('insurance_bond_on_file')->nullable();
            $table->boolean('insurance_bond_required')->nullable();
            $table->bigInteger('insurance_cargo_on_file')->nullable();
            $table->boolean('insurance_cargo_required')->nullable();
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
    }

    public function down(): void
    {
        Schema::dropIfExists('carrier_address_ids');
        Schema::dropIfExists('carrier_contact_history_logs');
        Schema::dropIfExists('carrier_contact_changes');
        Schema::dropIfExists('carrier_inspections');
        Schema::dropIfExists('carrier_crashes');
        Schema::dropIfExists('carrier_basics');
        Schema::dropIfExists('carriers');
        Schema::dropIfExists('carrier_insurance_histories');
        Schema::dropIfExists('carrier_equipment_histories');
        Schema::dropIfExists('carrier_company_snapshots');
        Schema::dropIfExists('carrier_contact_histories');
        Schema::dropIfExists('carrier_insurances');
        Schema::dropIfExists('carrier_fleet_summaries');
    }
};
