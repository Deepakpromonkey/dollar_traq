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
        Schema::create('carrier_change_logs', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('carrier_id');

            $table->string('table_name');

            $table->unsignedBigInteger('record_id')->nullable();

            $table->string('column_name');

            $table->longText('old_value')->nullable();

            $table->longText('new_value')->nullable();

            $table->timestamp('synced_at');

            $table->timestamps();

            $table->index('carrier_id');
            $table->index('table_name');
            $table->index('column_name');

            $table->foreign('carrier_id')
                ->references('id')
                ->on('carriers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrier_change_logs');
    }
};
