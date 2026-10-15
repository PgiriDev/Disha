<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('airline_id')->constrained('airlines')->restrictOnDelete();
            $table->string('flight_number', 10);
            $table->enum('flight_type', ['domestic', 'international'])->default('domestic');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['airline_id', 'flight_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
