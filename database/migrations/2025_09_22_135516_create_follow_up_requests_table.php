<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowUpRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('follow_up_requests', function (Blueprint $table) {
            $table->id();

            // لازم تتطابق مع نوع الـ id في جدول الأصل
            $table->unsignedInteger('patient_id'); // لأن patient.id = increments → int unsigned
            $table->unsignedInteger('visit_id')->nullable(); // لو جدول patient_visits معمول بـ increments
            $table->unsignedBigInteger('doctor_id')->nullable(); // employees.id = bigint unsigned
            $table->unsignedBigInteger('clinic_id')->nullable(); // لو adm_s_clinics.id معمول بـ increments

            $table->date('request_date');
            $table->date('followup_date');
            $table->text('notes')->nullable();

            $table->unsignedBigInteger('user_id')->nullable(); // users.id في لارافيل بيكون Bigint
            $table->timestamps();
            $table->softDeletes();

            // علاقات
            $table->foreign('patient_id')->references('id')->on('patient');
            $table->foreign('visit_id')->references('id')->on('patient_visits');
            $table->foreign('doctor_id')->references('id')->on('employees');
            $table->foreign('clinic_id')->references('id')->on('adm_s_clinics');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('follow_up_requests');
    }
}
