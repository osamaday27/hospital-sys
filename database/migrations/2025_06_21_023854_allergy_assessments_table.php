<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('allergy_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->unsignedBigInteger('reservation_id')->nullable();
            $table->unsignedBigInteger('user_add');

            $table->boolean('allergies_no')->default(false);
            $table->boolean('allergies_yes')->default(false);

            $table->boolean('allergy_food')->default(false);
            $table->boolean('allergy_medication')->default(false);
            $table->boolean('allergy_others')->default(false);
            $table->string('allergy_others_details')->nullable();

            $table->boolean('notify_doctor_yes')->default(false);
            $table->boolean('notify_doctor_no')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('allergy_assessments');
    }
};