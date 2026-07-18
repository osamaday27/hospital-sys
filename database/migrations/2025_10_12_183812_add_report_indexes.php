<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ===== store_movements =====
        Schema::table('store_movements', function (Blueprint $table) {
            // فلاتر النوع + التاريخ (لاحتساب الاستهلاك بالفترة)
            $table->index(['movement_type', 'movement_date'], 'idx_sm_type_date');

            // لتجميع/البحث على (product, store, batch) + الفرز بأحدث تاريخ
            $table->index(['product_id', 'store_id', 'batch_no', 'movement_date'], 'idx_sm_prod_store_batch_date');

            // لأخذ "آخر حركة" للصنف/الباتش عبر كل المخازن (عند التجميع)
            $table->index(['product_id', 'batch_no', 'movement_date'], 'idx_sm_prod_batch_date');

            // مفيدة للوصلات الشائعة
            $table->index('store_id',  'idx_sm_store');
            $table->index('units_id',  'idx_sm_units');
            $table->index('user_id',   'idx_sm_user');
            $table->index('reference_no', 'idx_sm_ref');

            // إن كنت تستخدم SoftDeletes فعليًا (والحقل موجود كما بالصورة)
            $table->index('deleted_at', 'idx_sm_deleted');
        });

        // ===== product_store_balances =====
        Schema::table('product_store_balances', function (Blueprint $table) {
            $table->index(['product_id', 'store_id', 'batch_no'], 'idx_psb_prod_store_batch');
            $table->index('expiry_date',  'idx_psb_expiry');
            $table->index('deleted_at',   'idx_psb_deleted');
        });

        // ===== products_data =====
        Schema::table('products_data', function (Blueprint $table) {
            $table->index('product_category_id', 'idx_p_category');
            $table->index('deleted_at',          'idx_p_deleted');
            // اختياري: لو تبحث كثيرًا بكود النظام
            $table->index('system_code',         'idx_p_syscode');
        });

        // ===== product_categories =====
        Schema::table('product_categories', function (Blueprint $table) {
            $table->index('deleted_at', 'idx_c_deleted');
        });
    }

    public function down(): void
    {
        // ===== store_movements =====
        Schema::table('store_movements', function (Blueprint $table) {
            $table->dropIndex('idx_sm_type_date');
            $table->dropIndex('idx_sm_prod_store_batch_date');
            $table->dropIndex('idx_sm_prod_batch_date');
            $table->dropIndex('idx_sm_store');
            $table->dropIndex('idx_sm_units');
            $table->dropIndex('idx_sm_user');
            $table->dropIndex('idx_sm_ref');
            $table->dropIndex('idx_sm_deleted');
        });

        // ===== product_store_balances =====
        Schema::table('product_store_balances', function (Blueprint $table) {
            $table->dropIndex('idx_psb_prod_store_batch');
            $table->dropIndex('idx_psb_expiry');
            $table->dropIndex('idx_psb_deleted');
        });

        // ===== products_data =====
        Schema::table('products_data', function (Blueprint $table) {
            $table->dropIndex('idx_p_category');
            $table->dropIndex('idx_p_deleted');
            $table->dropIndex('idx_p_syscode');
        });

        // ===== product_categories =====
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropIndex('idx_c_deleted');
        });
    }
};
