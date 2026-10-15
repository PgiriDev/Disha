<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->char('booking_reference', 6)->unique();
            $table->foreignId('passenger_id')->constrained('passengers')->restrictOnDelete();
            $table->foreignId('flight_schedule_id')->constrained('flight_schedules')->restrictOnDelete();
            $table->enum('travel_class', ['economy', 'business', 'first'])->default('economy');
            $table->string('seat_number', 5);
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->decimal('total_amount', 10, 2);
            $table->dateTime('booked_at');
            $table->dateTime('cancelled_at')->nullable();
            $table->timestamps();

            // Prevents the same seat being sold twice on the same flight schedule.
            $table->unique(['flight_schedule_id', 'seat_number']);
            $table->index(['passenger_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
