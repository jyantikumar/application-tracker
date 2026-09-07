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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->string('company_name', 255);
            $table->string('position', 255);
            
            // Salary fields for Remote (exact) vs Onsite/Hybrid (range)
            $table->float('salary', 10, 2)->nullable();
            $table->float('salary_min', 10, 2)->nullable();
            $table->float('salary_max', 10, 2)->nullable();
            
            $table->string('currency', 10)->default('PHP');
            $table->string('pay_rate_type', 50)->default('Monthly');
            $table->float('hours_per_week', 5, 2)->nullable()->default(40);
            
            $table->string('job_type', 255);
            $table->text('description'); 
            $table->string('pros', 255)->nullable();
            $table->string('cons', 255)->nullable();
            $table->string('city', 255);
            $table->text('ApplicationURL')->nullable();
            $table->string('source', 255);
            $table->string('status', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};