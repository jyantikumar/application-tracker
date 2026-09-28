<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')
                ->constrained('job_applications')
                ->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type'); // cover_letter | portfolio | cert | other
            $table->unsignedBigInteger('file_size');
            $table->timestamps();

            $table->index(['application_id', 'file_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
