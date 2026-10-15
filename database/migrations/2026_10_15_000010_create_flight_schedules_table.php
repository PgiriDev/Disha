<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flight_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_id')->constrained('flights')->cascadeOnDelete();
            $table->foreignId('aircraft_id')->constrained('aircraft')->restrictOnDelete();
            $table->foreignId('departure_airport_id')->constrained('airports')->restrictOnDelete();
            $table->foreignId('arrival_airport_id')->constrained('airports')->restrictOnDelete();
            $table->dateTime('scheduled_departure');
            $table->dateTime('scheduled_arrival');
            $table->decimal('base_fare', 10, 2);
            $table->enum('status', ['scheduled', 'delayed', 'departed', 'arrived', 'cancelled'])
                ->default('scheduled');
            $table->timestamps();

            $table->index(['flight_id', 'scheduled_departure']);
            $table->index(['departure_airport_id', 'scheduled_departure']);
            $table->index(['arrival_airport_id', 'scheduled_arrival']);
            $table->index(['aircraft_id', 'scheduled_departure']);
        });

        // A schedule must end after it starts. SQLite cannot add CHECK via ALTER TABLE,
        // so this constraint is applied on the production/MariaDB connection only.
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement(
                'ALTER TABLE flight_schedules '
                .'ADD CONSTRAINT chk_flight_schedules_time '
                .'CHECK (scheduled_arrival > scheduled_departure)'
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('flight_schedules');
    }
};
