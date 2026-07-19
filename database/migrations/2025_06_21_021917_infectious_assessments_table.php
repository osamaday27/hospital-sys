<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('infectious_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->unsignedBigInteger('reservation_id')->nullable();
            $table->unsignedBigInteger('user_add');

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

            $table->boolean('wear_mask')->default(false);
            $table->boolean('separate_patient')->default(false);
            $table->boolean('notify_doctor_action')->default(false);
            $table->boolean('accelerate_examination')->default(false);

            $table->boolean('notify_doctor_infectious_yes')->default(false);
            $table->boolean('notify_doctor_infectious_no')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('infectious_assessments');
    }
};
