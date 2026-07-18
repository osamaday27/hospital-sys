<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fall_risk_screenings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->unsignedBigInteger('reservation_id')->nullable();
            $table->unsignedBigInteger('user_add');

            $table->boolean('assist_patient')->default(false);
            $table->boolean('ensure_aids')->default(false);
            $table->boolean('review_medications')->default(false);
            $table->boolean('refer_conditions')->default(false);
            $table->boolean('avoid_crowding')->default(false);

            $table->boolean('advise_no_self_move')->default(false);
            $table->boolean('advise_no_sudden_stand')->default(false);
            $table->boolean('advise_no_left_alone_child')->default(false);
            $table->boolean('advise_report_dizziness')->default(false);

            $table->boolean('notify_doctor_yes')->default(false);
            $table->boolean('notify_doctor_no')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fall_risk_screenings');
    }
};