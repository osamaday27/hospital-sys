<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_candidates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requisition_id');
            $table->string('first_name');
            $table->string('second_name');
            $table->string('third_name');
            $table->string('fourth_name');
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female']);
            $table->string('national_id')->unique();
            $table->string('email')->unique();
            $table->unsignedBigInteger('city')->nullable();
            $table->unsignedInteger('nationality')->nullable();
            $table->string('cv_file')->nullable();
            $table->timestamps();

            $table->foreign('requisition_id')->references('id')->on('hr_requisitions')->onDelete('cascade');
            $table->foreign('city')->references('id')->on('city')->onDelete('set null');
            $table->foreign('nationality')->references('id')->on('nationalities_settings')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_candidates');
    }
};
