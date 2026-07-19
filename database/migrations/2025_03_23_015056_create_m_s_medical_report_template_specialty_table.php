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
        Schema::create('m_s_medical_report_template_specialty', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->unsignedBigInteger('specialty_id');
            $table->timestamps();

            $table->foreign('template_id')->references('id')->on('m_s_medical_report_templates')->onDelete('cascade');
            $table->foreign('specialty_id')->references('id')->on('specialties')->onDelete('cascade');

            // اختصار اسم الـ unique index لتجنب الخطأ
            $table->unique(['template_id', 'specialty_id'], 'template_specialty_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_s_medical_report_template_specialty');
    }
};
