<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interviews', function (Blueprint $Table) {
            $Table->id();
            $Table->foreignId('job_application_id')->constrained()->cascadeOnDelete();
            $Table->string('interview_type');
            $Table->dateTime('scheduled_at');
            $Table->enum('status', ['Scheduled', 'Completed', 'Cancelled'])->default('Scheduled');
            $Table->text('notes')->nullable();
            $Table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};