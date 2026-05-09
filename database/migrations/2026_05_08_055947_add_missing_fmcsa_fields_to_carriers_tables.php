<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =========================
        // carriers
        // =========================

        Schema::table('carriers', function (Blueprint $table) {

            $table->string('allowed_to_operate', 10)->nullable();

            $table->string('mcs150_outdated', 10)->nullable();

            $table->string('oos_date', 50)->nullable();

            $table->string('oos_rate_national_average_year', 50)->nullable();

            $table->string('iss_score', 50)->nullable();

            $table->boolean('is_passenger_carrier')->nullable();

            $table->string('snapshot_date', 50)->nullable();

            $table->string('status_code', 20)->nullable();

            $table->string('review_date', 50)->nullable();

            $table->string('review_type', 20)->nullable();

            $table->string('safety_rating', 20)->nullable();

            $table->string('safety_rating_date', 50)->nullable();

            $table->string('safety_review_date', 50)->nullable();

            $table->string('safety_review_type', 20)->nullable();

            $table->string('carrier_operation_code', 20)->nullable();

            $table->string('carrier_operation_desc', 255)->nullable();

            $table->string('census_type', 20)->nullable();

            $table->string('census_type_desc', 100)->nullable();

            $table->bigInteger('census_type_id')->nullable();

            $table->string('common_authority_status', 20)->nullable();

            $table->string('contract_authority_status', 20)->nullable();

            $table->string('broker_authority_status', 20)->nullable();

            $table->string('phy_city', 100)->nullable();

            $table->string('phy_country', 20)->nullable();

            $table->string('phy_state', 20)->nullable();

            $table->string('phy_street', 255)->nullable();

            $table->string('phy_zipcode', 20)->nullable();
        });



        // =========================
        // carrier_inspections
        // =========================

        Schema::table('carrier_inspections', function (Blueprint $table) {

            $table->decimal('driver_oos_rate', 10, 2)->nullable();

            $table->decimal('vehicle_oos_rate', 10, 2)->nullable();

            $table->decimal('hazmat_oos_rate', 10, 2)->nullable();
        });



        // =========================
        // carrier_fleet_summaries
        // =========================

        Schema::table('carrier_fleet_summaries', function (Blueprint $table) {

            $table->bigInteger('total_drivers')->nullable();
        });



        // =========================
        // carrier_insurances
        // =========================

        Schema::table('carrier_insurances', function (Blueprint $table) {

            $table->bigInteger('insurance_bipd_required_amount')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('carriers', function (Blueprint $table) {

            $table->dropColumn([
                'allowed_to_operate',
                'mcs150_outdated',
                'oos_date',
                'oos_rate_national_average_year',
                'iss_score',
                'is_passenger_carrier',
                'snapshot_date',
                'status_code',
                'review_date',
                'review_type',
                'safety_rating',
                'safety_rating_date',
                'safety_review_date',
                'safety_review_type',
                'carrier_operation_code',
                'carrier_operation_desc',
                'census_type',
                'census_type_desc',
                'census_type_id',
                'common_authority_status',
                'contract_authority_status',
                'broker_authority_status',
                'phy_city',
                'phy_country',
                'phy_state',
                'phy_street',
                'phy_zipcode'
            ]);
        });



        Schema::table('carrier_inspections', function (Blueprint $table) {

            $table->dropColumn([
                'driver_oos_rate',
                'vehicle_oos_rate',
                'hazmat_oos_rate'
            ]);
        });



        Schema::table('carrier_fleet_summaries', function (Blueprint $table) {

            $table->dropColumn('total_drivers');
        });



        Schema::table('carrier_insurances', function (Blueprint $table) {

            $table->dropColumn('insurance_bipd_required_amount');
        });
    }
};