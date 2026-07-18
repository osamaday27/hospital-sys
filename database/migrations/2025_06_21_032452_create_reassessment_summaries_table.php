<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reassessment_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->unsignedBigInteger('reservation_id')->nullable();
            $table->unsignedBigInteger('user_add');

            $table->enum('self_care', ['نعم','لا']);
            $table->enum('instructions_clear', ['نعم','لا']);
            $table->enum('doctor_alert', ['نعم','لا']);
            $table->enum('social_exam', ['لم يتغير','تغير']);
            $table->string('social_exam_details')->nullable();
            $table->enum('special_needs', ['لم يتغير','تغير']);
            $table->string('special_needs_details')->nullable();
            $table->enum('infectious_disease', ['نعم','لا']);
            $table->string('infectious_disease_details')->nullable();
            $table->enum('pain_check', ['لا يوجد','يوجد']);
            $table->string('pain_level')->nullable();
            $table->enum('allergy_check', ['لا يوجد','يوجد']);
            $table->string('allergy_details')->nullable();
            $table->enum('critical_results', ['نعم','لا']);
            $table->enum('nutrition_check', ['طبيعي','غير طبيعي']);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reassessment_summaries');
    }
};