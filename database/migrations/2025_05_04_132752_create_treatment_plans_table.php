<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('m_d_treatment_plans_dental', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('id_reservation')->nullable();
            $table->unsignedBigInteger('id_visit')->nullable();
            $table->string('type_visit')->nullable();
            $table->string('tooth')->nullable();
            $table->integer('seq')->nullable();
            $table->text('clinical_radiographic_findings')->nullable();
            $table->string('department')->nullable();
            $table->text('treatment_options')->nullable();
            $table->string('faculty_signature')->nullable();
            $table->string('status')->default('active');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_d_treatment_plans_dental');
    }
};
