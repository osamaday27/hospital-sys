<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('section_store_returns', function (Blueprint $table) {
            $table->id();

            // كود المستند (مثال: RFS-YYYY-00001)
            $table->string('code', 50)->index();

            // رقم طلب الصرف من جدول department_item_requests_stores (اختياري)
            $table->string('request_number', 50)->nullable()->index();

            // القسم (hr_sections) والمخزن
            $table->unsignedBigInteger('section_id');
            $table->unsignedBigInteger('store_id');

            // التاريخ
            $table->date('return_date');

            // بيانات الصنف
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('unit_id');

            // الباتش / الكمية
            $table->string('batch_no', 100)->nullable();
            $table->decimal('quantity', 18, 6);

            // ملاحظات + المستخدم المنشئ
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // المفاتيح الخارجية
            $table->foreign('section_id')
                  ->references('id')->on('hr_sections')
                  ->restrictOnDelete()->cascadeOnUpdate();

            $table->foreign('store_id')
                  ->references('id')->on('store_main_date')
                  ->restrictOnDelete()->cascadeOnUpdate();

            $table->foreign('product_id')
                  ->references('id')->on('products_data')
                  ->restrictOnDelete()->cascadeOnUpdate();

            $table->foreign('unit_id')
                  ->references('id')->on('units')
                  ->restrictOnDelete()->cascadeOnUpdate();

            $table->foreign('created_by')
                  ->references('id')->on('users')
                  ->nullOnDelete()->cascadeOnUpdate();

            // فهارس مفيدة
            $table->index(['code', 'product_id']);
            $table->index(['store_id', 'product_id', 'batch_no']);
            $table->index(['section_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_store_returns');
    }
};
