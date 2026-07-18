<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('rad_s_packages', function (Blueprint $table) {
            // 🗓️ إضافة أعمدة تواريخ
            $table->date('start_date')->nullable()->after('description');
            $table->date('end_date')->nullable()->after('start_date');

            // حذف العمود القديم
            $table->dropColumn('duration_days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('rad_s_packages', function (Blueprint $table) {
            // رجوع للعمود القديم
            $table->integer('duration_days')->nullable();

            // حذف الأعمدة الجديدة
            $table->dropColumn(['start_date', 'end_date']);
        });
    }
};
