<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_cheques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained('fin_banks')->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained('fin_bank_accounts')->nullOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('cheque_number')->unique();
            $table->date('issue_date'); // تاريخ الإصدار
            $table->date('cheque_date'); // تاريخ الاستحقاق
            $table->decimal('amount', 18, 2);
            $table->string('currency', 10)->default('SAR');
            $table->enum('type', ['incoming', 'outgoing'])->default('incoming');
            $table->enum('status', ['new', 'under_collection', 'deposited', 'cleared', 'returned', 'cancelled'])->default('new');
            $table->string('beneficiary')->nullable(); // المستفيد
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['cheque_number', 'cheque_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_cheques');
    }
};