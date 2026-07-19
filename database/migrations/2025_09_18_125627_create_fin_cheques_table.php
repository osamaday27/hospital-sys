<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fin_cheques')) {
            return;
        }

        Schema::create('fin_cheques', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('bank_id');

            $table->unsignedBigInteger('account_id')->nullable();

            $table->unsignedBigInteger('branch_id');

            $table->string('cheque_number')->unique();

            $table->date('issue_date');

            $table->date('cheque_date');

            $table->decimal('amount',18,2);

            $table->string('currency',10)->default('SAR');

            $table->enum('type',[
                'incoming',
                'outgoing'
            ])->default('incoming');

            $table->enum('status',[
                'new',
                'under_collection',
                'deposited',
                'cleared',
                'returned',
                'cancelled'
            ])->default('new');

            $table->string('beneficiary')->nullable();

            $table->text('notes')->nullable();

            $table->unsignedBigInteger('user_id')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index(['cheque_number','cheque_date','status']);
        });

        Schema::table('fin_cheques', function (Blueprint $table) {

            if (Schema::hasTable('fin_banks')) {
                $table->foreign('bank_id')
                    ->references('id')
                    ->on('fin_banks')
                    ->cascadeOnDelete();
            }

            if (Schema::hasTable('fin_bank_accounts')) {
                $table->foreign('account_id')
                    ->references('id')
                    ->on('fin_bank_accounts')
                    ->nullOnDelete();
            }

            if (Schema::hasTable('branches')) {
                $table->foreign('branch_id')
                    ->references('id')
                    ->on('branches')
                    ->cascadeOnDelete();
            }

            if (Schema::hasTable('users')) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('fin_cheques')) {
            return;
        }

        Schema::table('fin_cheques', function (Blueprint $table) {

            try {
                $table->dropForeign(['bank_id']);
            } catch (\Throwable $e) {}

            try {
                $table->dropForeign(['account_id']);
            } catch (\Throwable $e) {}

            try {
                $table->dropForeign(['branch_id']);
            } catch (\Throwable $e) {}

            try {
                $table->dropForeign(['user_id']);
            } catch (\Throwable $e) {}
        });

        Schema::dropIfExists('fin_cheques');
    }
};