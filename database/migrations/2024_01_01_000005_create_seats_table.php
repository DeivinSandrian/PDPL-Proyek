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
        Schema::create('seats', function (Blueprint $table) {
            $table->integer('seat_id', true);
            $table->integer('vehicle_id')->nullable();
            $table->string('seat_number', 5)->nullable();
            $table->string('seat_class', 20)->nullable();

            $table->index('vehicle_id');
            $table->foreign('vehicle_id')->references('vehicle_id')->on('vehicles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
