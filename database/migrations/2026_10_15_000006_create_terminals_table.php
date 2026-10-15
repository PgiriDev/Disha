<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terminals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('airport_id')->constrained('airports')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 10);
            $table->unsignedInteger('passenger_capacity')->nullable();
            $table->timestamps();

            $table->unique(['airport_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terminals');
    }
};
