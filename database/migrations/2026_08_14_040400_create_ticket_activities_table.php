<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->enum('type', [
                'created',
                'status_change',
                'assigned',
                'scheduled',
                'forwarded',
                'comment',
                'diagnosis',
                'daily_report',
                'attachment',
                'verified',
                'rated',
                'reopened',
                'cancelled',
            ]);
            $table->text('description');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['ticket_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_activities');
    }
};
