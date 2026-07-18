<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('adm_b_questionnaires', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id');
            $table->unsignedInteger('patient_visits_id');
            $table->unsignedBigInteger('questionnair');
            $table->enum('status', ['closed', 'under_review', 'new'])->default('new');
            $table->json('attachments')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->timestamps();
            $table->softDeletes();


            // تعريف العلاقات الخارجية
            $table->foreign('patient_id')->references('id')->on('patient')->onDelete('cascade');
            $table->foreign('patient_visits_id')->references('id')->on('patient_visits')->onDelete('cascade');
            $table->foreign('questionnair')->references('id')->on('adm_s_questionnaires')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('closed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('adm_b_questionnaires');
    }
};
