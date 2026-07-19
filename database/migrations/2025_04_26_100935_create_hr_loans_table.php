<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hr_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->enum('loan_type', ['instant', 'loan'])->default('instant');
            $table->integer('amount')->unsigned();
            $table->date('loan_date');
            $table->integer('no_installments')->default(1);
            $table->decimal('installment_value', 10, 2);
            $table->integer('remaining_installments');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_loans');
    }
};
