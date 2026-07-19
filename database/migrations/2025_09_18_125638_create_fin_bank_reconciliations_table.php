<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_bank_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained('fin_banks')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('fin_bank_accounts')->cascadeOnDelete();
            $table->string('period', 20); // YYYY-MM
            $table->decimal('opening_balance', 18, 2)->default(0);
            $table->decimal('closing_balance', 18, 2)->default(0);
            $table->decimal('difference', 18, 2)->default(0);
            $table->foreignId('reconciled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('reconciliation_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['bank_id', 'account_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_bank_reconciliations');
    }
};
