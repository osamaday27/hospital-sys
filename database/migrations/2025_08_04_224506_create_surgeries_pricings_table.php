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
  Schema::create('operation_contract_pricing', function (Blueprint $table) {
    $table->id();
    $table->foreignId('contract_id')->constrained('fin_contract_last_level')->onDelete('cascade');
    $table->foreignId('surgery_id')->constrained('fin_services')->onDelete('cascade'); // أو جدول العمليات
    $table->decimal('surgeon_fee', 10, 2)->nullable();
    $table->decimal('assistant_fee', 10, 2)->nullable();
    $table->decimal('anesthesia_fee', 10, 2)->nullable();
    $table->string('anesthesia_type')->nullable(); // عام - نصفي - موضعي
    $table->decimal('nursing_fee', 10, 2)->nullable();
    $table->decimal('operation_room_fee', 10, 2)->nullable();
    $table->decimal('additional_time_fee', 10, 2)->nullable();
    $table->decimal('supplies_cost', 10, 2)->nullable();
    $table->decimal('other_services', 10, 2)->nullable();
    $table->decimal('discount', 10, 2)->nullable();
    $table->decimal('vat', 10, 2)->nullable();
    $table->decimal('total_price', 10, 2)->nullable(); // سعر العملية النهائي
    $table->enum('status', ['active', 'inactive'])->default('active');
    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
    $table->softDeletes();
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operation_contract_pricing');
    }
};
