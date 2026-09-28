<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')
                ->constrained('job_applications')
                ->cascadeOnDelete();
            $table->foreignId('interview_id')
                ->nullable()
                ->constrained('interviews')
                ->cascadeOnDelete();
            $table->string('type'); // follow_up | interview | stale_app
            $table->unsignedSmallInteger('lead_hours')->nullable(); // 24 | 1 for interview alerts
            $table->timestamp('scheduled_for');
            $table->timestamp('sent_at')->nullable();
            $table->boolean('is_dismissed')->default(false);
            $table->timestamps();

            $table->index('scheduled_for');
            $table->index(['type', 'is_dismissed', 'sent_at']);
            $table->index(['application_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
