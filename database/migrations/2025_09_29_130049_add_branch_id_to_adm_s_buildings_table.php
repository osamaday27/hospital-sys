<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('adm_s_buildings', function (Blueprint $table) {
            // إضافة العمود branch_id
            $table->unsignedBigInteger('branch_id')->nullable()->after('id');

            // ربط المفتاح الأجنبي مع جدول الفروع (branches)
            $table->foreign('branch_id')
                  ->references('id')
                  ->on('branches')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('adm_s_buildings', function (Blueprint $table) {
            // حذف المفتاح الأجنبي أولاً ثم العمود
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};