<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrPenaltiesTable extends Migration
{
    public function up()
    {
        Schema::create('hr_penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade'); 
            $table->foreignId('violation_id')->constrained('hr_violations')->onDelete('cascade'); 
            $table->date('date'); 
            $table->text('reason'); 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_penalties');
    }
}
