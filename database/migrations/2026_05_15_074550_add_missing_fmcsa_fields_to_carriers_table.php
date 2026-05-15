<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('carriers', function (Blueprint $table) {
            $table->string('allowed_to_operate')->nullable();
            $table->string('status_code')->nullable();
            $table->string('mcs150_outdated')->nullable();
            $table->string('passenger_carrier')->nullable();
            $table->string('safety_review_type')->nullable();
            $table->string('phy_country')->nullable();
            $table->string('phy_city')->nullable();
            $table->string('phy_state')->nullable();
            $table->string('phy_street')->nullable();
            $table->string('phy_zipcode')->nullable();
            $table->string('safety_rating')->nullable();
            $table->string('safety_review_date')->nullable();
            $table->string('driver_oos_total')->nullable();
            $table->string('hazmat_oos_rate')->nullable();
            $table->string('vehicle_oos_total')->nullable();
            $table->string('oos_rate_national_average_year')->nullable();
            $table->string('oos_date')->nullable();
            $table->string('driverOosRate')->nullable();
            $table->string('bipdRequiredAmount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carriers', function (Blueprint $table) {
            //
        });
    }
};
