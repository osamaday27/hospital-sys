<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fin_bank_permissions', function (Blueprint $table) {
            if (!Schema::hasColumn('fin_bank_permissions', 'can_clear_cheque')) {
                $table->boolean('can_clear_cheque')->default(false)->after('can_approve_cheque');
            }
            if (!Schema::hasColumn('fin_bank_permissions', 'can_bounce_cheque')) {
                $table->boolean('can_bounce_cheque')->default(false)->after('can_cancel_cheque');
            }
            if (!Schema::hasColumn('fin_bank_permissions', 'can_reconcile_cheque')) {
                $table->boolean('can_reconcile_cheque')->default(false)->after('can_bounce_cheque');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fin_bank_permissions', function (Blueprint $table) {
            $table->dropColumn(['can_clear_cheque','can_bounce_cheque','can_reconcile_cheque']);
        });
    }
};