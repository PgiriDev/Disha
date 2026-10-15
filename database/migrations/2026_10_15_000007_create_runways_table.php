<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('runways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('airport_id')->constrained('airports')->cascadeOnDelete();
            $table->string('designation', 10);
            $table->unsignedInteger('length_m');
            $table->unsignedInteger('width_m')->nullable();
            $table->string('surface')->default('asphalt');
            $table->enum('status', ['active', 'maintenance', 'closed'])->default('active');
            $table->timestamps();

            $table->unique(['airport_id', 'designation']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('runways');
    }
};
