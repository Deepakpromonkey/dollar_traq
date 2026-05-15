<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carrier_operations', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('carrier_id');

            $table->string('carrier_operation_code')->nullable();

            $table->string('carrier_operation_desc')->nullable();

            $table->timestamps();

            $table->foreign('carrier_id')
                ->references('id')
                ->on('carriers')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carrier_operations');
    }
};
