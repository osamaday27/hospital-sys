<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fin_bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained('fin_banks')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('fin_bank_accounts')->cascadeOnDelete();
            $table->date('transaction_date');
            $table->enum('type', ['deposit', 'withdrawal', 'transfer', 'fees', 'interest']);
            $table->decimal('amount', 18, 2);
            $table->string('reference')->nullable();
            $table->unsignedInteger('related_invoice_id')->nullable();

            $table->foreign('related_invoice_id')->references('id')->on('fin_invoices')->onDelete('set null');

            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['transaction_date', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_bank_transactions');
    }
};