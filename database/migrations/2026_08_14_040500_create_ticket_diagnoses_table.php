<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_diagnoses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('technician_id')->constrained('users')->restrictOnDelete();
            $table->text('diagnosis_text');
            $table->text('root_cause')->nullable();
            $table->text('action_taken')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $table->timestamps();

            $table->unique(['ticket_id', 'technician_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_diagnoses');
    }
};
