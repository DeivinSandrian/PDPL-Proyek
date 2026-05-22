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
        Schema::create('bookings', function (Blueprint $table) {
            $table->integer('booking_id', true);
            $table->integer('user_id')->nullable();
            $table->integer('schedule_id')->nullable();
            $table->string('booking_code', 10)->unique();
            $table->enum('booking_channel', ['online', 'offline']);
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'expired'])->default('pending');
            $table->dateTime('hold_expired_at')->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->index('user_id');
            $table->index('schedule_id');
            $table->foreign('schedule_id')->references('schedule_id')->on('schedules');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
