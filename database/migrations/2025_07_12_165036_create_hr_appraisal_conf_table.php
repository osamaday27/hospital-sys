<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hr_appraisal_conf', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('hr_jobs')->onDelete('cascade');
            $table->timestamps();
        });

        // Create pivot table for many-to-many relationship
        Schema::create('hr_appraisal_conf_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appraisal_conf_id')->constrained('hr_appraisal_conf')->onDelete('cascade');
            $table->foreignId('term_id')->constrained('hr_appraisal_terms_conf')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_appraisal_conf_terms');
        Schema::dropIfExists('hr_appraisal_conf');
    }
};
