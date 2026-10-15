<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            // One check-in per ticket (1:1).
            $table->foreignId('ticket_id')->unique()->constrained('tickets')->cascadeOnDelete();
            $table->string('boarding_pass_number', 20)->unique();
            $table->dateTime('checked_in_at');
            $table->foreignId('gate_id')->nullable()->constrained('gates')->nullOnDelete();
            $table->enum('status', ['checked_in', 'boarded', 'no_show'])->default('checked_in');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_ins');
    }
};
