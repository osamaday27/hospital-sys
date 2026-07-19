<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // التحقق من أن الجدول غير موجود أولاً
        if (!Schema::hasTable('fin_cheques')) {
            Schema::create('fin_cheques', function (Blueprint $table) {
                $table->id();
                
                $table->unsignedBigInteger('bank_id');
                $table->foreign('bank_id')->references('id')->on('fin_banks')->cascadeOnDelete();
                
                $table->unsignedBigInteger('account_id')->nullable();
                $table->foreign('account_id')->references('id')->on('fin_bank_accounts')->nullOnDelete();
                
                $table->unsignedBigInteger('branch_id');
                $table->foreign('branch_id')->references('id')->on('branches')->cascadeOnDelete();
                
                $table->string('cheque_number')->unique();
                $table->date('issue_date'); 
                $table->date('cheque_date'); 
                $table->decimal('amount', 18, 2);
                $table->string('currency', 10)->default('SAR');
                $table->enum('type', ['incoming', 'outgoing'])->default('incoming');
                $table->enum('status', ['new', 'under_collection', 'deposited', 'cleared', 'returned', 'cancelled'])->default('new');
                $table->string('beneficiary')->nullable(); 
                $table->text('notes')->nullable();
                
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
                
                $table->timestamps();
                $table->softDeletes();

                $table->index(['cheque_number', 'cheque_date', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_cheques');
    }
};
