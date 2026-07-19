<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fin_bank_cheques', function (Blueprint $table) {
            // احذف الـ unique القديم
            $table->dropUnique(['cheque_number']);

            // أضف unique مركب على (bank_account_id, cheque_number)
            $table->unique(['bank_account_id', 'cheque_number'], 'unique_cheque_per_account');
        });
    }

    public function down(): void
    {
        Schema::table('fin_bank_cheques', function (Blueprint $table) {
            $table->dropUnique('unique_cheque_per_account');
            $table->unique('cheque_number');
        });
    }
};
