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
        Schema::create('routes', function (Blueprint $table) {
            $table->integer('route_id', true);
            $table->string('origin_city', 100);
            $table->string('destination_city', 100);
            $table->integer('distance_km')->nullable();
            $table->time('duration')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
