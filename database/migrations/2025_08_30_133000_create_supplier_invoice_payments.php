<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supplier_invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_invoice_id')->constrained('supplier_invoices')->onDelete('cascade');
            $table->foreignId('locker_id')->constrained('fin_s_locker')->onDelete('cascade');
            $table->decimal('amount', 15, 2)->default(0);
            $table->enum('payment_method', ['cash','visa','bank'])->default('cash');
            $table->string('reference_no')->nullable();
            $table->string('bank_name')->nullable();
            $table->text('bank_details')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_invoice_payments');
    }
};
