<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asset_conversions', function (Blueprint $table) {
            $table->id();

            // مراجع أساسية
            $table->unsignedBigInteger('receiving_inspecting_id');
            $table->unsignedBigInteger('receiving_item_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('store_id');

            // بيانات الدُفعة والصلاحية
            $table->string('batch_no')->nullable();
            $table->date('expiry_date')->nullable();

            // وحدة الحركة/التسجيل (قد تكون أصغر وحدة للمنتج)
            $table->unsignedBigInteger('unit_id')->nullable();

            // الكميات (بالوحدة المدخلة + بوحدة الأساس)
            $table->decimal('qty_in_unit', 18, 3)->default(0);
            $table->decimal('qty_in_base', 18, 3)->default(0);

            // تكلفة للوحدة الأساسية (من بند المحضر)
            $table->decimal('cost_price', 18, 4)->nullable();

            // الأصل الناتج من التحويل (إن وُجد/أُنشئ)
            $table->unsignedBigInteger('asset_item_id')->nullable();

            // بيانات عامة
            $table->timestamp('conversion_date')->useCurrent();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('notes')->nullable();

            // حالة التحويل + الإلغاء
            $table->enum('status', ['active', 'cancelled'])->default('active');
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();

            // النظام
            $table->timestamps();
            $table->softDeletes();

            // فهارس لتحسين الاستعلامات
            $table->index(['receiving_inspecting_id']);
            $table->index(['receiving_item_id']);
            $table->index(['product_id']);
            $table->index(['store_id']);
            $table->index(['batch_no']);
            $table->index(['expiry_date']);
            $table->index(['asset_item_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_conversions');
    }
};
