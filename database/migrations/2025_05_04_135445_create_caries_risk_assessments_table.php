<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('caries_risk_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('id_reservation')->nullable();
            $table->unsignedBigInteger('id_visit')->nullable();
            $table->string('type_visit')->nullable();

            $table->json('high_risk')->nullable();
            $table->json('moderate_risk')->nullable();
            $table->json('protective_factors')->nullable();

            $table->string('brushing_frequency')->nullable();
            $table->string('brushing_method')->nullable();
            $table->string('brush_type')->nullable();
            $table->string('interproximal_cleansing')->nullable();
            $table->string('caries_risk_overall')->nullable();
            $table->boolean('recommendations_given')->default(false);
            $table->date('date_given')->nullable();
            $table->string('faculty_signature')->nullable();
            $table->date('date_reviewed')->nullable();
            $table->string('status')->default('active');
            $table->string('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caries_risk_assessments');
    }
};