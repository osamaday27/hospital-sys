<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hr_investigations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('investigation_address');
            $table->string('investigation_type');
            $table->text('investigation_details');
            $table->text('action_taken');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_investigations');
    }
}; 
