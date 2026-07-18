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
        Schema::create('hr_allocations', function (Blueprint $table) {
            $table->id();
            $table->string('allocation_name');
            $table->unsignedBigInteger('vacation_type');
            $table->enum('mode', ['by_employee', 'by_department', 'by_hospital']);
            $table->date('valid_from');
            $table->date('valid_to');
            $table->tinyInteger('amount');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('hr_allocations', function (Blueprint $table) {
            $table->foreign('vacation_type')
                  ->references('id')
                  ->on('hr_vacation_types')
                  ->onDelete('cascade');
        });

        Schema::create('employee_hr_allocation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('hr_allocation_id')->constrained('hr_allocations')->onDelete('cascade');
            $table->decimal('remaining_balance', 8, 2)->default(0);
            $table->timestamps();
            
            // Optional: Add composite index
            $table->unique(['employee_id', 'hr_allocation_id']);
        });

        Schema::table('employee_hr_allocation', function (Blueprint $table) {
            $table->dropForeign(['hr_allocation_id']);
            $table->dropForeign(['employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
    
        Schema::dropIfExists('employee_hr_allocation');
        Schema::dropIfExists('hr_allocations');
        
        Schema::enableForeignKeyConstraints();
    }
};
