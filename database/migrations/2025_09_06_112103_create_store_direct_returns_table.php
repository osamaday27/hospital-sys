<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('store_direct_returns', function (Blueprint $table) {
            $table->bigIncrements('id');

            // كود حركة الارتجاع
            $table->string('code', 50);

            // ربط اختياري مع حركة التحويل الأصلية
            $table->unsignedBigInteger('store_transfer_id')->nullable()->index();
            // الاحتفاظ برقم التحويل (transfer_number) لسهولة التتبع
            $table->string('transfer_number', 50)->nullable()->index();

            // من/إلى مخزن
            $table->unsignedBigInteger('store_from_id');
            $table->unsignedBigInteger('store_to_id');

            // التاريخ
            $table->date('transfer_date');

            // الصنف + الوحدة
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('units_id');

            // الكمية
            $table->decimal('quantity', 18, 4);

            // الباتش (اختياري)
            $table->string('batch_no', 100)->nullable();

            // المستخدم مُدخل البيانات
            $table->unsignedBigInteger('user_id');

            // ملاحظات
            $table->text('notes')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // قيود و علاقات
            $table->foreign('store_transfer_id')->references('id')->on('store_transfers')->onDelete('set null');

            $table->foreign('store_from_id')->references('id')->on('store_main_date')->onDelete('restrict');
            $table->foreign('store_to_id')->references('id')->on('store_main_date')->onDelete('restrict');

            $table->foreign('product_id')->references('id')->on('products_data')->onDelete('restrict');
            $table->foreign('units_id')->references('id')->on('units')->onDelete('restrict');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');

            // فهارس مساعدة
            $table->index(['store_from_id', 'store_to_id']);
            $table->index(['product_id', 'units_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_direct_returns');
    }
};
