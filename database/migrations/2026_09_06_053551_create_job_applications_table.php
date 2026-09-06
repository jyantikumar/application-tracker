<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('company_name', 255);
            $table->string('position', 255);
            $table->float('salary', 10, 2);
            $table->string('currency', 10)->default('PHP'); // USD, PHP, EUR, GBP, AUD, etc.
            $table->string('pay_rate_type', 50)->default('Monthly'); // Monthly or Hourly
            $table->float('hours_per_week', 5, 2)->nullable()->default(40); // For conversion calculations
            $table->string('job_type', 255);
            $table->text('description'); 
            $table->string('pros', 255)->nullable();
            $table->string('cons', 255)->nullable();
            $table->string('city', 255);
            $table->string('ApplicationURL');
            $table->string('source', 255);
            $table->string('status', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};