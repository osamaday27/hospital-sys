<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('supplier_payment_installments', function (Blueprint $table) {
            // خزنة الدفع
            $table->foreignId('locker_id')
                ->nullable()
                ->constrained('fin_s_locker')
                ->onDelete('set null');

            // البنك
            $table->foreignId('bank_id')
                ->nullable()
                ->constrained('fin_banks')
                ->onDelete('set null');

            // حساب البنك
            $table->foreignId('bank_account_id')
                ->nullable()
                ->constrained('fin_bank_accounts')
                ->onDelete('set null');

            // دفتر الشيك
            $table->foreignId('cheque_book_id')
                ->nullable()
                ->constrained('fin_cheque_books')
                ->onDelete('set null');

            // ورقة الشيك
            $table->foreignId('cheque_leaf_id')
                ->nullable()
                ->constrained('fin_cheque_leaves')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_payment_installments', function (Blueprint $table) {
            $table->dropForeign(['locker_id']);
            $table->dropColumn('locker_id');

            $table->dropForeign(['bank_id']);
            $table->dropColumn('bank_id');

            $table->dropForeign(['bank_account_id']);
            $table->dropColumn('bank_account_id');

            $table->dropForeign(['cheque_book_id']);
            $table->dropColumn('cheque_book_id');

            $table->dropForeign(['cheque_leaf_id']);
            $table->dropColumn('cheque_leaf_id');
        });
    }
};