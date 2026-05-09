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
        Schema::create('passengers', function (Blueprint $table) {
            $table->integer('passenger_id', true);
            $table->integer('booking_id')->nullable();
            $table->string('full_name', 100)->nullable();
            $table->string('identity_number', 50)->nullable();
            $table->string('phone', 20)->nullable();

            $table->index('booking_id');
            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passengers');
    }
};
