<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('adm_s_questions', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->unsignedBigInteger('type_id'); // مرتبط بجدول adm_s_questionnaires
            $table->unsignedBigInteger('answer_id')->nullable(); // مرتبط بجدول adm_s_answer_types
            $table->boolean('text')->default(false);
            $table->string('created_by'); // اسم المستخدم الذي أضاف البيانات
            $table->timestamps();
            $table->softDeletes();

            // العلاقات
            $table->foreign('type_id')->references('id')->on('adm_s_questionnaires')->onDelete('cascade');
            $table->foreign('answer_id')->references('id')->on('adm_s_answer_types')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('adm_s_questions');
    }
};
