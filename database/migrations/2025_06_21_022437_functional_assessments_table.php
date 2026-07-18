<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('functional_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->unsignedBigInteger('reservation_id')->nullable();
            $table->unsignedBigInteger('user_add');

            $table->boolean('live_alone')->default(false);
            $table->boolean('no_problem')->default(false);
            $table->boolean('total_dependence')->default(false);
            $table->boolean('movement_problems')->default(false);

            $table->boolean('no_assistance')->default(false);
            $table->boolean('walker')->default(false);
            $table->boolean('wheelchair')->default(false);
            $table->boolean('crutches')->default(false);
            $table->boolean('other_assistance')->default(false);

            $table->boolean('notify_doctor_functional_yes')->default(false);
            $table->boolean('notify_doctor_functional_no')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('functional_assessments');
    }
};