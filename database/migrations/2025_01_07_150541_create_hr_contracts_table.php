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
        Schema::create('hr_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_name');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade'); 
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('job_id')->constrained('hr_jobs')->onDelete('cascade'); 
            $table->foreignId('department_id')->constrained('hr_department')->onDelete('cascade'); 
            $table->enum('salary_structure_type', ['physician', 'nursing', 'admin']);
            $table->enum('status', ['open', 'expired', 'closed'])->default('open');

            $table->float('basic_salary');
            $table->float('allowances')->default(0);
            $table->float('medical_insurance')->default(0);
            $table->string('social_insurance_number')->nullable();
            $table->float('shohdaa')->default(0);
            $table->float('social_insurance')->default(0);
            $table->float('tax')->default(0);
            $table->float('net_salary');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_contracts');
    }
};
