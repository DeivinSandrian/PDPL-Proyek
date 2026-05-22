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
        Schema::create('stop_points', function (Blueprint $table) {
            $table->integer('stop_point_id', true);
            $table->string('name', 100);
            $table->text('address')->nullable();
            $table->enum('type', ['pickup', 'dropoff']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stop_points');
    }
};
