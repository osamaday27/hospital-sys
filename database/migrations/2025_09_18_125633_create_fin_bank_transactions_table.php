<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fin_bank_transactions')) {
            return;
        }

        Schema::create('fin_bank_transactions', function (Blueprint $table) {

            $table->id();

            // البنوك
            $table->unsignedBigInteger('bank_id');

            // سيتم ربطه لاحقاً بعد إنشاء جدول الحسابات البنكية
            $table->unsignedBigInteger('account_id')->nullable();

            $table->date('transaction_date');

            $table->enum('type', [
                'deposit',
                'withdrawal',
                'transfer',
                'fees',
                'interest'
            ]);

            $table->decimal('amount', 18, 2);

            $table->string('reference')->nullable();

            // لأن fin_invoices.id = INT UNSIGNED
            $table->unsignedInteger('related_invoice_id')->nullable();

            $table->text('notes')->nullable();

            $table->unsignedBigInteger('user_id')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index(['transaction_date', 'type']);

            /*
             |----------------------------------------
             | Foreign Keys
             |----------------------------------------
             */

            $table->foreign('bank_id')
                ->references('id')
                ->on('fin_banks')
                ->onDelete('cascade');

            // لا يوجد جدول fin_bank_accounts حالياً
            // سيتم إضافته بعد إنشاء الجدول
            /*
            $table->foreign('account_id')
                ->references('id')
                ->on('fin_bank_accounts')
                ->onDelete('cascade');
            */

            $table->foreign('related_invoice_id')
                ->references('id')
                ->on('fin_invoices')
                ->nullOnDelete();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('fin_bank_transactions')) {
            return;
        }

        Schema::table('fin_bank_transactions', function (Blueprint $table) {

            try {
                $table->dropForeign(['bank_id']);
            } catch (\Throwable $e) {}

            try {
                $table->dropForeign(['related_invoice_id']);
            } catch (\Throwable $e) {}

            try {
                $table->dropForeign(['user_id']);
            } catch (\Throwable $e) {}

            // account_id لا يوجد له Foreign Key حالياً
        });

        Schema::dropIfExists('fin_bank_transactions');
    }
};