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

            $table->boolean('mcs150_outdated')->nullable();

            $table->string('broker_authority_status')->nullable();

            $table->string('common_authority_status')->nullable();

            $table->string('contract_authority_status')->nullable();

            $table->boolean('passenger_carrier')->nullable();

            $table->string('review_type')->nullable();

            $table->string('safety_review_type')->nullable();

            $table->string('oos_rate_national_average_year')->nullable();

            $table->date('oos_date')->nullable();

            $table->string('phy_country')->nullable();

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
