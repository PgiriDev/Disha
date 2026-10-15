<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            // One ticket per booking (1:1).
            $table->foreignId('booking_id')->unique()->constrained('bookings')->cascadeOnDelete();
            $table->string('ticket_number', 20)->unique();
            $table->decimal('fare', 10, 2);
            $table->enum('status', ['issued', 'checked_in', 'used', 'cancelled', 'refunded'])
                ->default('issued');
            $table->dateTime('issued_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
