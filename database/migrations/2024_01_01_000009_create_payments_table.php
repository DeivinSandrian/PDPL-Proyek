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
        Schema::create('payments', function (Blueprint $table) {
            $table->integer('payment_id', true);
            $table->integer('booking_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('method', 50)->nullable();
            $table->string('status', 20)->nullable();
            $table->string('gateway_transaction_id', 100)->nullable();
            $table->dateTime('paid_at')->nullable();

            $table->index('booking_id');
            $table->foreign('booking_id')->references('booking_id')->on('bookings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
