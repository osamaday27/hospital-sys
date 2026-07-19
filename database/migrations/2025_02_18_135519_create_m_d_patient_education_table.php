<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_d_patient_education', function (Blueprint $table) {
            $table->id();
            $table->date('assessment_date')->nullable();
            $table->time('assessment_time')->nullable();
            $table->string('department')->nullable();
            $table->string('education_recipient')->nullable();
            $table->string('relation_to_patient')->nullable();
            $table->string('education_level')->nullable();
            $table->string('learning_ability')->nullable();
            $table->string('main_language')->nullable();
            $table->string('other_language')->nullable();
            $table->json('barriers')->nullable(); // لحفظ العوائق كـ JSON
            $table->json('interventions')->nullable(); // لحفظ التدخلات كـ JSON
            $table->string('nurse_name')->nullable();
            $table->date('nurse_date')->nullable();
            $table->time('nurse_time')->nullable();
            $table->json('education_sessions')->nullable(); // لحفظ جلسات التثقيف كـ JSON
            $table->json('education_method')->nullable(); // لحفظ طريقة التعليم كـ JSON
            $table->string('evaluation')->nullable();
            $table->string('action_taken')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_d_patient_education');
    }
};
