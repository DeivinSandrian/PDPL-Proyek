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
        Schema::create('booking_seats', function (Blueprint $table) {
            $table->integer('booking_seat_id', true);
            $table->integer('booking_id')->nullable();
            $table->integer('seat_id')->nullable();
            $table->decimal('price_at_booking', 10, 2)->nullable();

            $table->index('booking_id');
            $table->index('seat_id');
            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
            $table->foreign('seat_id')->references('seat_id')->on('seats');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_seats');
    }
};
