<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('m_d_dental_medical_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('id_reservation')->nullable();
            $table->unsignedBigInteger('id_visit')->nullable();
            $table->string('type_visit')->nullable();

            $table->json('medical_conditions')->nullable();
            $table->json('medications')->nullable();
            $table->boolean('medical_treatment')->default(false);
            $table->string('medical_treatment_note')->nullable();
            $table->boolean('serious_operation')->default(false);
            $table->string('serious_operation_note')->nullable();
            $table->boolean('taking_medication')->default(false);
            $table->string('taking_medication_note')->nullable();
            $table->boolean('smoker')->default(false);
            $table->string('smoke_count')->nullable();
            $table->json('female')->nullable();
            $table->json('allergies')->nullable();
            

            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_d_dental_medical_histories');
    }
};
