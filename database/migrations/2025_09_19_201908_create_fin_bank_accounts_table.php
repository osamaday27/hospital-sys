<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fin_bank_accounts', function (Blueprint $table) {
            $table->foreignId('gl_account_id')->nullable()->constrained('fin_s_gl_accounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('fin_bank_accounts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('gl_account_id');
        });
    }
};
