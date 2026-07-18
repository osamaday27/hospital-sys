<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('supplier_payment_installments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('supplier_invoice_id')
          ->constrained('supplier_invoices')
          ->onDelete('cascade');
    $table->foreignId('supplier_payment_id')
          ->nullable()
          ->constrained('supplier_invoice_payments')
          ->onDelete('cascade');
    $table->enum('method', ['cash', 'cheque', 'bank_transfer'])->default('cash');
    $table->decimal('amount', 12, 2);
    $table->string('reference_no')->nullable();
    $table->string('bank_name')->nullable();
    $table->string('bank_details')->nullable();
    $table->date('due_date')->nullable();
    $table->date('paid_date')->nullable();
    $table->enum('status', ['pending', 'paid', 'bounced'])->default('pending');
    $table->text('notes')->nullable();
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
        Schema::dropIfExists('supplier_payment_installments');
    }
};