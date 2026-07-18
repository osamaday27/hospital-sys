<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('m_b_diseases_his_pat', function (Blueprint $table) {
            if (!Schema::hasColumn('m_b_diseases_his_pat', 'diag_flag')) {
                $table->string('diag_flag', 10)->default('initial')->index()->after('remarks');
            }
        });
    }

    public function down(): void
    {
        Schema::table('m_b_diseases_his_pat', function (Blueprint $table) {
            if (Schema::hasColumn('m_b_diseases_his_pat', 'diag_flag')) {
                $table->dropColumn('diag_flag');
            }
        });
    }
};