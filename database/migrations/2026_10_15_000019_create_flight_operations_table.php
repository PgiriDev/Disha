<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flight_operations', function (Blueprint $table) {
            $table->id();
            // One operations record per flight schedule (actual times kept separate
            // from the scheduled times stored on flight_schedules).
            $table->foreignId('flight_schedule_id')->unique()->constrained('flight_schedules')->cascadeOnDelete();
            $table->dateTime('actual_departure')->nullable();
            $table->dateTime('actual_arrival')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flight_operations');
    }
};
