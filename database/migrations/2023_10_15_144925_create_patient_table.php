<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePatientTable extends Migration
{
    public function up()
    {
        Schema::create('patient', function (Blueprint $table) {
            $table->increments('id');
            $table->string('medical_number')->nullable();
            $table->string('full_name')->nullable();
            $table->string('first_name_ar')->nullable();
            $table->string('second_name_ar')->nullable();
            $table->string('third_name_ar')->nullable();
            $table->string('fourth_name_ar')->nullable();
            $table->string('first_name_en')->nullable();
            $table->string('second_name_en')->nullable();
            $table->string('third_name_en')->nullable();
            $table->string('fourth_name_en')->nullable();
            $table->string('national_id')->nullable();
            $table->string('passport')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('phone_number2')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('the_best_way_to_communicate')->nullable();
            $table->string('insurance_contracting')->nullable();
            $table->string('nationality')->nullable();
            $table->string('country')->nullable();
            $table->string('governorate')->nullable();
            $table->string('city')->nullable();
            $table->string('area')->nullable();
            $table->string('full_address')->nullable();
            $table->string('img')->nullable();
            $table->string('status')->nullable();
            $table->string('reason_patient_stop')->nullable();
            $table->string('default_branch')->nullable();
            $table->string('user_id')->nullable();
            $table->string('account_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::drop('patient');
    }
}