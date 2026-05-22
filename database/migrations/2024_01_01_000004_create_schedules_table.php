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
        Schema::create('schedules', function (Blueprint $table) {
            $table->integer('schedule_id', true);
            $table->integer('route_id')->nullable();
            $table->integer('vehicle_id')->nullable();
            $table->dateTime('departure_time');
            $table->dateTime('arrival_estimate')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('status', 20)->default('available');

            $table->index('route_id');
            $table->index('vehicle_id');
            $table->foreign('route_id')->references('route_id')->on('routes');
            $table->foreign('vehicle_id')->references('vehicle_id')->on('vehicles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
