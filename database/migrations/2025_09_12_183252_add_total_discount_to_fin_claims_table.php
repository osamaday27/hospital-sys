<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fin_claims', function (Blueprint $table) {
            if (!Schema::hasColumn('fin_claims', 'total_discount')) {
                $table->decimal('total_discount', 12, 2)->default(0)->after('total_contract_share');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fin_claims', function (Blueprint $table) {
            $table->dropColumn('total_discount');
        });
    }
};