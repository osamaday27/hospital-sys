<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fin_bank_reconciliations')) {
            return;
        }

        Schema::create('fin_bank_reconciliations', function (Blueprint $table) {

            $table->id();

            // البنك
            $table->unsignedBigInteger('bank_id');

            // الحساب البنكي (سيتم ربطه لاحقاً بعد إنشاء جدول fin_bank_accounts)
            $table->unsignedBigInteger('account_id')->nullable();

            // الفترة
            $table->string('period', 20);

            // الأرصدة
            $table->decimal('opening_balance', 18, 2)->default(0);
            $table->decimal('closing_balance', 18, 2)->default(0);
            $table->decimal('difference', 18, 2)->default(0);

            // المستخدم
            $table->unsignedBigInteger('reconciled_by')->nullable();

            $table->date('reconciliation_date')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->unique(
                ['bank_id', 'account_id', 'period'],
                'bank_reconciliation_unique'
            );
        });

        Schema::table('fin_bank_reconciliations', function (Blueprint $table) {

            // علاقة البنك
            if (Schema::hasTable('fin_banks')) {
                $table->foreign('bank_id')
                    ->references('id')
                    ->on('fin_banks')
                    ->onDelete('cascade');
            }

            /*
             |-------------------------------------------------
             | لا يوجد جدول fin_bank_accounts حالياً
             | لذلك لا ننشئ Foreign Key حتى يتم إنشاء الجدول
             |-------------------------------------------------
             */

            /*
            if (Schema::hasTable('fin_bank_accounts')) {
                $table->foreign('account_id')
                    ->references('id')
                    ->on('fin_bank_accounts')
                    ->onDelete('cascade');
            }
            */

            // المستخدم
            if (Schema::hasTable('users')) {
                $table->foreign('reconciled_by')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('fin_bank_reconciliations')) {
            return;
        }

        Schema::table('fin_bank_reconciliations', function (Blueprint $table) {

            try {
                $table->dropForeign(['bank_id']);
            } catch (\Throwable $e) {}

            try {
                $table->dropForeign(['reconciled_by']);
            } catch (\Throwable $e) {}

            // لا يوجد Foreign Key لـ account_id حالياً
        });

        Schema::dropIfExists('fin_bank_reconciliations');
    }
};