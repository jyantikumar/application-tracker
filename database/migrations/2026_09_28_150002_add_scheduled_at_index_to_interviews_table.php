<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->index('scheduled_at');
        });

        Schema::table('job_applications', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->dropIndex(['scheduled_at']);
        });

        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['status']);
        });
    }
};
