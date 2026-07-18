<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // استبدل الاسم صراحةً إن كان مختلفًا لديك
        $tableName = (new \App\Models\supply_chain\receivinginspectingitem)->getTable();

        Schema::table($tableName, function (Blueprint $table) {
            // وحدة القبول المختارة (units.id) — بدون FK لتجنب مشاكل اختلاف أسماء الجداول
            if (!Schema::hasColumn($table->getTable(), 'accepted_unit_id')) {
                $table->unsignedBigInteger('accepted_unit_id')->nullable()->after('unit_id');
            }

            // قيم العرض (بوحدة المستخدم) بجانب القيم الأساسية الحالية
            if (!Schema::hasColumn($table->getTable(), 'delivered_qty_display')) {
                $table->decimal('delivered_qty_display', 18, 3)->nullable()->after('delivered_qty');
            }
            if (!Schema::hasColumn($table->getTable(), 'accepted_qty_display')) {
                $table->decimal('accepted_qty_display', 18, 3)->nullable()->after('accepted_qty');
            }
            if (!Schema::hasColumn($table->getTable(), 'rejected_qty_display')) {
                $table->decimal('rejected_qty_display', 18, 3)->nullable()->after('rejected_qty');
            }
        });
    }

    public function down(): void
    {
        $tableName = (new \App\Models\supply_chain\receivinginspectingitem)->getTable();

        Schema::table($tableName, function (Blueprint $table) {
            foreach (['accepted_unit_id','delivered_qty_display','accepted_qty_display','rejected_qty_display'] as $col) {
                if (Schema::hasColumn($table->getTable(), $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
