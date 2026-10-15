<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('terminal_id')->constrained('terminals')->cascadeOnDelete();
            $table->string('code', 10);
            $table->enum('status', ['available', 'occupied', 'closed'])->default('available');
            $table->timestamps();

            $table->unique(['terminal_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gates');
    }
};
