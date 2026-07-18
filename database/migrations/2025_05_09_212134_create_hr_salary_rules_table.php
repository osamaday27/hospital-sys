<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hr_salary_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['addition', 'deduction']);
            $table->enum('rule_type', ['absence', 'lateness', 'penalties', 'loans', 'incentive', 'overtime', 'manual']);
            $table->boolean('added_automatically')->default(false);
            $table->integer('sequence')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_salary_rules');
    }
};


