<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hr_social_insurance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->boolean('insurance_status')->default(true);
            $table->date('subscription_date');
            $table->string('insurance_office');
            $table->string('insurance_number')->unique();
            $table->boolean('health_status')->default(true);
            $table->string('insurance_company');
            $table->decimal('main_salary', 10, 2);
            $table->decimal('shared_salary', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_social_insurance');
    }
}; 
