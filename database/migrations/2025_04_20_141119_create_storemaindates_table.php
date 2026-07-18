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
    public function up()
    {

        Schema::create('store_main_date', function (Blueprint $table) {
            $table->id();

            $table->string('manual_code')->unique();
            $table->string('name_ar');
            $table->string('name_en');
            $table->enum('status', ['active', 'inactive'])->nullable();

            $table->enum('store_type', ['main', 'sub']); // نوع المخزن: رئيسي أو فرعي

            $table->string('address_ar')->nullable();
            $table->string('address_en')->nullable();

            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade'); // علاقة بجدول الفروع

            $table->json('contact_methods')->nullable(); // وسيلة الاتصال (JSON)

            $table->enum('pricing_method', ['cost', 'selling'])->nullable(); // طريقة الصرف

            $table->decimal('daily_avg_consumption')->nullable(); // متوسط الاستهلاك اليومي
            $table->decimal('profit_margin')->nullable(); // هامش الربح من سعر التكلفة (%)
            $table->integer('min_order_quantity')->nullable(); // الحد الأدنى للطلب
            $table->integer('max_order_quantity')->nullable(); // الحد الأقصى للطلب

            $table->decimal('opening_balance')->default(0); // الرصيد الافتتاحي

            $table->boolean('is_inventory_allowed')->default(true); // مسموح بالجرد
            $table->boolean('is_virtual')->default(false); // هل المخزن وهمي
            $table->json('related_stores_id')->nullable(); // المخازن المرتبطة (JSON Array)

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('store_main_date');
    }
};
