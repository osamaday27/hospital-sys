<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fin_bank_cheques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained('fin_bank_accounts')->cascadeOnDelete();
            $table->string('cheque_number')->unique();
            $table->decimal('amount', 15, 2);
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->string('payee')->nullable(); // المستفيد
            $table->enum('status', ['issued','approved','cleared','cancelled','bounced','reconciled'])->default('issued');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_bank_cheques');
    }
};
