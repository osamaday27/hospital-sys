<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderCommitteesTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_order_committees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')
                  ->constrained('purchase_orders')
                  ->onDelete('cascade');
            $table->foreignId('employee_id')
                  ->constrained('employees')
                  ->onDelete('cascade');
            $table->foreignId('department_id')
                  ->nullable()
                  ->constrained('hr_department')  
                  ->nullOnDelete();
            $table->string('job_position')->nullable();
            $table->boolean('is_chair')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_order_committees');
    }
}
