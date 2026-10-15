<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flight_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_schedule_id')->constrained('flight_schedules')->cascadeOnDelete();
            $table->string('reason');
            $table->foreignId('cancelled_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->dateTime('cancelled_at');
            $table->decimal('compensation', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flight_cancellations');
    }
};
