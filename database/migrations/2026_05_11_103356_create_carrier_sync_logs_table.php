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
       Schema::create('carrier_sync_logs', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('carrier_id');

            $table->string('status')->nullable();

            $table->integer('total_changes')->default(0);

            $table->text('message')->nullable();

            $table->longText('api_response')->nullable();

            $table->timestamp('started_at')->nullable();

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index('carrier_id');

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
        Schema::dropIfExists('carrier_sync_logs');
    }
};
