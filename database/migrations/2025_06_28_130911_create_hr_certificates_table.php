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
        Schema::create('hr_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('certificate_name');
            $table->unsignedBigInteger('certificate_type_id');
            $table->date('graduation_date')->nullable();
            $table->unsignedBigInteger('university_id')->nullable();
            $table->string('certificate_file')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('certificate_type_id')->references('id')->on('hr_certificates_types')->onDelete('cascade');
            $table->foreign('university_id')->references('id')->on('hr_universities')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_certificates');
    }
}; 
