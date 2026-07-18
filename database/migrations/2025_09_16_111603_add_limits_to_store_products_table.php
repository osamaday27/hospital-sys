<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_products', function (Blueprint $table) {
            // الحد الأدنى للطلب + الوحدة
            $table->decimal('order_min_qty', 18, 6)->nullable()->after('status');
            $table->foreignId('order_min_unit_id')
                  ->nullable()
                  ->after('order_min_qty')
                  ->constrained('product_units')
                  ->nullOnDelete();

            // الحد الأقصى للطلب + الوحدة
            $table->decimal('order_max_qty', 18, 6)->nullable()->after('order_min_unit_id');
            $table->foreignId('order_max_unit_id')
                  ->nullable()
                  ->after('order_max_qty')
                  ->constrained('product_units')
                  ->nullOnDelete();

            // حد طلب الاستعاضة + الوحدة
            $table->decimal('reorder_point_qty', 18, 6)->nullable()->after('order_max_unit_id');
            $table->foreignId('reorder_point_unit_id')
                  ->nullable()
                  ->after('reorder_point_qty')
                  ->constrained('product_units')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('store_products', function (Blueprint $table) {
            // فك القيود أولاً
            $table->dropForeign(['order_min_unit_id']);
            $table->dropForeign(['order_max_unit_id']);
            $table->dropForeign(['reorder_point_unit_id']);

            // حذف الأعمدة
            $table->dropColumn([
                'order_min_qty',
                'order_min_unit_id',
                'order_max_qty',
                'order_max_unit_id',
                'reorder_point_qty',
                'reorder_point_unit_id',
            ]);
        });
    }
};
