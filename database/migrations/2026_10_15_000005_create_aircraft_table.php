<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aircraft', function (Blueprint $table) {
            $table->id();
            $table->foreignId('airline_id')->constrained('airlines')->restrictOnDelete();
            $table->string('registration_number', 10)->unique();
            $table->string('manufacturer');
            $table->string('model');
            $table->unsignedSmallInteger('seat_capacity');
            $table->unsignedSmallInteger('business_capacity')->default(0);
            $table->unsignedSmallInteger('economy_capacity')->default(0);
            $table->enum('status', ['active', 'maintenance', 'retired'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aircraft');
    }
};
