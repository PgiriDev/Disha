<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flight_gate_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_schedule_id')->constrained('flight_schedules')->cascadeOnDelete();
            $table->foreignId('gate_id')->constrained('gates')->restrictOnDelete();
            $table->dateTime('assigned_from');
            $table->dateTime('assigned_to')->nullable();
            $table->enum('status', ['active', 'released'])->default('active');
            $table->timestamps();

            // Supports the overlap-detection query used by GateAssignmentService.
            $table->index(['gate_id', 'status', 'assigned_from']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flight_gate_assignments');
    }
};
