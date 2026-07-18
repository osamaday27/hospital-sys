<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmBComplaintsTable extends Migration
{
    public function up()
    {
        Schema::create('adm_b_complaints', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->boolean('registered_patient')->nullable();

            // علاقات خارجية مع تعديل لقبول null
            $table->unsignedInteger('patient_id')->nullable();
            $table->unsignedInteger('visit_id')->nullable();
            // إضافة الحقول الجديدة بعد visit_id وقابلة للقيم null
            $table->string('patient_name')->nullable();
            $table->integer('patient_age')->nullable();
            $table->string('patient_gender')->nullable();

            $table->unsignedBigInteger('problem_id');
            // قيم محددة للأولوية
            $table->enum('priority', ['urgent', 'normal', 'necessary']);
            $table->date('complaint_date')->nullable();
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('sections_id');
            $table->text('complaint')->nullable();
            $table->text('attachments')->nullable();
            $table->string('complaint_place')->nullable();
            $table->json('contact_information')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // تعريف المفاتيح الخارجية
            $table->foreign('patient_id')
                  ->references('id')
                  ->on('patient')
                  ->onDelete('cascade');

            $table->foreign('visit_id')
                  ->references('id')
                  ->on('patient_visits')
                  ->onDelete('cascade');

            $table->foreign('problem_id')
                  ->references('id')
                  ->on('adm_s_complaint_problems')
                  ->onDelete('cascade');

            $table->foreign('department_id')
                  ->references('id')
                  ->on('hr_department')
                  ->onDelete('cascade');

            $table->foreign('sections_id')
                  ->references('id')
                  ->on('hr_sections')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('adm_b_complaints');
    }
}
