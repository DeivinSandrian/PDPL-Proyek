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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->integer('vehicle_id', true); // auto-increment primary key
            $table->string('plate_number', 20);
            $table->string('vehicle_type', 50)->nullable();
            $table->integer('capacity');
            $table->string('status', 20)->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
