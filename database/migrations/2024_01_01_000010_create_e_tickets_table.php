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
        Schema::create('e_tickets', function (Blueprint $table) {
            $table->integer('ticket_id', true);
            $table->integer('booking_id')->nullable();
            $table->string('ticket_code', 20)->nullable()->unique();
            $table->text('qr_code')->nullable();
            $table->dateTime('issued_at')->useCurrent();

            $table->index('booking_id');
            $table->foreign('booking_id')->references('booking_id')->on('bookings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_tickets');
    }
};
