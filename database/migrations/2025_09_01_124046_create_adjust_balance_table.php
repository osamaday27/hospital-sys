<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('adjust_balance', function (Blueprint $table) {
            $table->bigIncrements('id');

            // الكود (مرجعي/تسلسلي داخلي)
            $table->string('code', 50)->unique();

            // المخزن
            $table->unsignedBigInteger('store_id');

            // تاريخ ووقت الحركة
            $table->dateTime('movement_datetime')->useCurrent()->index();

            // نوع التسوية: increase أو decrease
            $table->enum('adjust_type', ['increase', 'decrease'])->index();

            // الصنف + وحدة الصنف (من product_units)
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_unit_id')->nullable();

            // الكمية
            $table->decimal('quantity', 18, 5)->default(0);

            // الباتش (إن وُجد)
            $table->string('batch_no', 100)->nullable();

            // الرصيد (يمكن استخدامه لتسجيل الرصيد بعد التسوية)
            $table->decimal('balance', 18, 5)->nullable();

            // سعر البيع/التكلفة وقت التسوية
            $table->decimal('sale_price', 18, 2)->nullable();
            $table->decimal('cost_price', 18, 2)->nullable();

            // تاريخ الانتهاء (إن وجد)
            $table->date('expiry_date')->nullable();

            // ملاحظات اختيارية + تتبع منشئ/معدل السجل (اختياري)
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // العلاقات (FKs)
            $table->foreign('store_id')
                  ->references('id')->on('store_main_date')  // اسم الجدول عندك كده
                  ->restrictOnDelete();

            $table->foreign('product_id')
                  ->references('id')->on('products_data')
                  ->restrictOnDelete();

            $table->foreign('product_unit_id')
                  ->references('id')->on('product_units')
                  ->nullOnDelete();

            // فهارس مساعدة
            $table->index(['store_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adjust_balance');
    }
};
