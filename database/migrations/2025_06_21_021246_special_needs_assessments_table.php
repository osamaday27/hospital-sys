<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('special_needs_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->unsignedBigInteger('reservation_id')->nullable();
            $table->unsignedBigInteger('user_add');

            $table->boolean('no_exercise')->default(false);
            $table->boolean('overweight')->default(false);
            $table->boolean('hates_school')->default(false);
            $table->boolean('family_issues')->default(false);

            $table->boolean('normal_elderly')->default(false);
            $table->boolean('cannot_count')->default(false);
            $table->boolean('doesnt_know_facility')->default(false);

            $table->boolean('physical_disability_yes')->default(false);
            $table->string('physical_disability_details')->nullable();

            $table->boolean('weak_immunity_yes')->default(false);
            $table->string('weak_immunity_reason')->nullable();

            $table->boolean('chronic_pain_yes')->default(false);

            $table->boolean('notify_doctor_special_yes')->default(false);
            $table->boolean('notify_doctor_special_no')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('special_needs_assessments');
    }
};