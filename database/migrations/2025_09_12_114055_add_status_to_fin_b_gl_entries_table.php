<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fin_b_gl_entries', function (Blueprint $table) {
            $table->enum('status', ['draft','posted','cancelled'])->default('draft')->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('fin_b_gl_entries', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};