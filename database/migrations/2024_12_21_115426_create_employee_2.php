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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // Meta Data
			$table->string('full_name')->nullable();
			$table->string('first_name_ar')->nullable();
			$table->string('second_name_ar')->nullable();
			$table->string('third_name_ar')->nullable();
			$table->string('fourth_name_ar')->nullable();
			$table->string('first_name_en')->nullable();
			$table->string('second_name_en')->nullable();
			$table->string('third_name_en')->nullable();
			$table->string('fourth_name_en')->nullable();
            $table->string('employee_photo')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('identification_number')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('religion')->nullable();
            $table->string('nationality')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('full_address')->nullable();
            // Contact Info
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('landline')->nullable();
            $table->string('fax')->nullable();
            $table->string('emergency_name')->nullable();
            $table->string('emergency_number')->nullable();
            $table->string('relationship')->nullable();
            // Employment Data
            $table->string('department')->nullable();
            $table->string('section')->nullable();
            $table->string('job_position')->nullable();
            $table->date('contract_open_date')->nullable();
            $table->date('hiring_date')->nullable();
            $table->date('contract_end_date')->nullable();
            // Hiring Documents
            $table->string('cv')->nullable();
            $table->string('militery')->nullable();
            $table->string('graduation_certificate')->nullable();
            $table->string('birth_certificate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
