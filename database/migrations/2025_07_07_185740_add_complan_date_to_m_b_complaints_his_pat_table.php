<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('m_b_complaints_his_pat', function (Blueprint $table) {
            $table->date('complan_date')->nullable()->after('complaints'); 
            // هنا after('complaint') لو تحب تحدد مكان العمود
            $table->text('complants_id')->nullable()->after('complan_date'); // إذا كنت تريد إضافة عمود complaints_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_b_complaints_his_pat', function (Blueprint $table) {
            $table->dropColumn('complan_date');
            $table->dropColumn('complants_id'); // إذا كنت تريد حذف عمود complaints أيضًا
        });
    }
};