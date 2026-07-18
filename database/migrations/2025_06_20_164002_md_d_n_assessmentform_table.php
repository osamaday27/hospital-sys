<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('md_d_reassessment_forms', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->unsignedBigInteger('reservation_id')->nullable();

            // فحص الأمراض المعدية
            $table->boolean('cough_no')->default(false);
            $table->boolean('cough_yes')->default(false);
            $table->string('cough_details')->nullable();

            $table->boolean('fever_no')->default(false);
            $table->boolean('fever_yes')->default(false);
            $table->string('fever_details')->nullable();

            $table->boolean('rash_no')->default(false);
            $table->boolean('rash_yes')->default(false);
            $table->string('rash_details')->nullable();

            $table->boolean('travel_no')->default(false);
            $table->boolean('travel_yes')->default(false);
            $table->string('travel_details')->nullable();

            // الإجراءات المتخذة
            $table->boolean('wear_mask')->default(false);
            $table->boolean('separate_patient')->default(false);
            $table->boolean('notify_doctor_action')->default(false);
            $table->boolean('accelerate_examination')->default(false);

            $table->boolean('doctor_notified')->default(false);

            // الفحص الحركي/الوظيفي
            $table->boolean('live_alone')->default(false);
            $table->boolean('no_problem')->default(false);
            $table->boolean('total_dependence')->default(false);
            $table->boolean('movement_problems')->default(false);

            $table->boolean('no_assistance')->default(false);
            $table->boolean('walker')->default(false);
            $table->boolean('wheelchair')->default(false);
            $table->boolean('crutches')->default(false);
            $table->boolean('other_assistance')->default(false);

            $table->boolean('doctor_notified_functional')->default(false);

            // فحص الألم
            $table->boolean('pain_no')->default(false);
            $table->boolean('pain_yes')->default(false);

            // العلامات الحيوية
            $table->string('temperature')->nullable();
            $table->string('respiratory_rate')->nullable();
            $table->string('blood_pressure')->nullable();
            $table->string('pulse')->nullable();
            $table->string('random_sugar')->nullable();
            $table->string('weight')->nullable();
            $table->string('height')->nullable();

            // مخاطر السقوط
            $table->boolean('help_patient_when_needed')->default(false);
            $table->boolean('ensure_appropriate_aids')->default(false);
            $table->boolean('review_medications')->default(false);
            $table->boolean('refer_to_specialist')->default(false);
            $table->boolean('avoid_crowding')->default(false);

            $table->boolean('dont_move_alone')->default(false);
            $table->boolean('dont_sudden_standing')->default(false);
            $table->boolean('dont_leave_child_alone')->default(false);
            $table->boolean('alert_medical_team')->default(false);

            // ملخص التقييم التمريضي
            $table->string('self_care', 10)->nullable();
            $table->string('instructions_clear', 10)->nullable();
            $table->string('doctor_alert_summary', 10)->nullable();
            $table->string('social_exam', 20)->nullable();
            $table->string('social_exam_details')->nullable();
            $table->string('special_needs_summary', 10)->nullable();
            $table->string('special_needs_details')->nullable();
            $table->string('infectious_disease_summary', 10)->nullable();
            $table->string('infectious_disease_details')->nullable();
            $table->string('pain_check_summary', 20)->nullable();
            $table->string('pain_check_details')->nullable();
            $table->string('allergy_check', 10)->nullable();
            $table->string('allergy_details')->nullable();
            $table->string('critical_results', 10)->nullable();
            $table->string('nutrition_check', 20)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('md_d_reassessment_forms');
    }
};