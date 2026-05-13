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
       Schema::create('carrier_sync_states', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('carrier_id');

            $table->timestamp('last_synced_at')->nullable();

            $table->timestamp('next_sync_at')->nullable();

            $table->integer('sync_attempts')->default(0);

            $table->boolean('is_syncing')->default(false);

            $table->timestamp('last_success_at')->nullable();

            $table->string('last_hash')->nullable();

            $table->timestamps();

            $table->unique('carrier_id');

            $table->index('last_synced_at');
            $table->index('next_sync_at');

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
        Schema::dropIfExists('carrier_sync_states');
    }
};
