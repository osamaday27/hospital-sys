<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('discharge_plannings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->unsignedBigInteger('reservation_id')->nullable();
            $table->unsignedBigInteger('user_add');

            $table->string('care_place')->nullable();
            $table->boolean('help_at_home_yes')->default(false);
            $table->boolean('help_at_home_no')->default(false);
            $table->boolean('self_care_yes')->default(false);
            $table->boolean('self_care_no')->default(false);
            $table->boolean('instructions_clear_yes')->default(false);
            $table->boolean('instructions_clear_no')->default(false);

            $table->boolean('need_assist_reminder')->default(false);
            $table->boolean('need_assist_medication')->default(false);
            $table->boolean('need_assist_stairs')->default(false);
            $table->boolean('need_assist_toilet')->default(false);
            $table->boolean('need_assist_bathing')->default(false);

            $table->boolean('doctor_alert_yes')->default(false);
            $table->boolean('doctor_alert_no')->default(false);

            $table->boolean('social_exam_normal')->default(false);
            $table->boolean('social_exam_abnormal')->default(false);
            $table->string('social_exam_details')->nullable();

            $table->boolean('special_needs_yes')->default(false);
            $table->boolean('special_needs_no')->default(false);
            $table->string('special_needs_details')->nullable();

            $table->boolean('infectious_yes')->default(false);
            $table->boolean('infectious_no')->default(false);
            $table->string('infectious_details')->nullable();

            $table->boolean('functional_normal')->default(false);
            $table->boolean('functional_abnormal')->default(false);

            $table->boolean('pain_no')->default(false);
            $table->boolean('pain_yes')->default(false);
            $table->string('pain_degree')->nullable();

            $table->boolean('allergy_no')->default(false);
            $table->boolean('allergy_yes')->default(false);
            $table->string('allergy_details')->nullable();

            $table->boolean('critical_results_yes')->default(false);
            $table->boolean('critical_results_no')->default(false);

            $table->boolean('nutrition_normal')->default(false);
            $table->boolean('nutrition_abnormal')->default(false);

            $table->string('nurse_signature')->nullable();
            $table->string('nurse_id')->nullable();
            $table->date('nurse_date')->nullable();
            $table->time('nurse_time')->nullable();

            $table->string('doctor_signature')->nullable();
            $table->string('doctor_id')->nullable();
            $table->date('doctor_date')->nullable();
            $table->time('doctor_time')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('discharge_plannings');
    }
};