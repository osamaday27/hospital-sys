<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_bom_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('bom_id')->index();
            $table->unsignedInteger('line_no')->default(1)->index();

            // نوع العنصر
            $table->enum('item_type', ['supply','drug','resource','misc'])->default('supply')->index();

            // مراجع اختيارية
            $table->unsignedBigInteger('inventory_item_id')->nullable()->index();   // إن كان عندك جدول items
            $table->unsignedBigInteger('resource_rate_id')->nullable()->index();    // من جدول resource_rates
            $table->unsignedBigInteger('cost_center_id')->nullable()->index();      // مركز التكلفة

            // تفاصيل العنصر
            $table->json('description')->nullable();      // translatable optional
            $table->string('unit', 50)->nullable();
            $table->decimal('qty', 18, 4)->default(1);
            $table->decimal('unit_cost', 18, 4)->default(0);
            $table->decimal('total_cost', 18, 2)->default(0);

            // للموارد (اختياري)
            $table->unsignedInteger('duration_minutes')->nullable(); // لو item_type = resource

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('bom_id')->references('id')->on('service_boms')->cascadeOnDelete();
            // المراجع الأخرى نجعلها اختيارية بدون FK لو الجداول غير جاهزة
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_bom_items');
    }
};