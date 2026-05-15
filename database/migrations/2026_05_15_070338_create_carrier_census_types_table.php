<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carrier_census_types', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('carrier_id');

            $table->string('census_type')->nullable();

            $table->string('census_type_desc')->nullable();

            $table->integer('census_type_id')->nullable();

            $table->timestamps();

            $table->foreign('carrier_id')
                ->references('id')
                ->on('carriers')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carrier_census_types');
    }
};
