<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('fin_b_gl_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('fin_b_gl_entries', 'group_key')) {
                $table->string('group_key')->nullable()->after('description');
            }
            if (!Schema::hasColumn('fin_b_gl_entries', 'is_settled')) {
                $table->boolean('is_settled')->default(false)->after('group_key');
            }
            if (!Schema::hasColumn('fin_b_gl_entries', 'settled_at')) {
                $table->timestamp('settled_at')->nullable()->after('is_settled');
            }
        });
    }

    public function down(): void {
        Schema::table('fin_b_gl_entries', function (Blueprint $table) {
            if (Schema::hasColumn('fin_b_gl_entries', 'settled_at')) {
                $table->dropColumn('settled_at');
            }
            if (Schema::hasColumn('fin_b_gl_entries', 'is_settled')) {
                $table->dropColumn('is_settled');
            }
            if (Schema::hasColumn('fin_b_gl_entries', 'group_key')) {
                $table->dropColumn('group_key');
            }
        });
    }
};