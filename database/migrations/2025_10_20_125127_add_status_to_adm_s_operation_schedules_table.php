<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('adm_s_operation_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('adm_s_operation_schedules', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('duration')
                    ->comment('حالة اليوم: active = مفعّل، inactive = غير مفعّل');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('adm_s_operation_schedules', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};