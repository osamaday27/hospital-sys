<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('adm_b_questionnaires_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('questionnair_id');
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('answer_id')->nullable();
            $table->text('notes')->nullable();
            $table->text('text')->nullable();
            $table->timestamps();
            $table->softDeletes();


            // تعريف العلاقات الخارجية
            $table->foreign('questionnair_id')->references('id')->on('adm_b_questionnaires')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('adm_s_questions')->onDelete('cascade');
            $table->foreign('answer_id')->references('id')->on('adm_s_answer_values')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('adm_b_questionnaires_details');
    }
};
