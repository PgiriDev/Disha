<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('baggage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->string('tag_number', 20)->unique();
            $table->decimal('weight_kg', 5, 2);
            $table->enum('type', ['checked', 'carry_on'])->default('checked');
            $table->enum('status', ['checked_in', 'loaded', 'in_transit', 'delivered', 'lost'])
                ->default('checked_in');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('baggage');
    }
};
