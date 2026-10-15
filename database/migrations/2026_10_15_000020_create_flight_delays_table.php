<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flight_delays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_schedule_id')->constrained('flight_schedules')->cascadeOnDelete();
            $table->unsignedSmallInteger('delay_minutes');
            $table->string('reason');
            $table->foreignId('reported_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->dateTime('reported_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flight_delays');
    }
};
