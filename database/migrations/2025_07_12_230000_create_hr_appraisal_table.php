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
        Schema::create('hr_appraisal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('employee_code');
            $table->unsignedBigInteger('job_id');
            $table->unsignedBigInteger('department');
            $table->date('hiring_date');
            $table->string('appraisal_interval');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('job_id')->references('id')->on('hr_jobs')->onDelete('cascade');
            $table->foreign('department')->references('id')->on('hr_department')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_appraisal');
    }
}; 
