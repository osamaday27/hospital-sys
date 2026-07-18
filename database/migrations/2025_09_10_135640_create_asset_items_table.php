<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('asset_items', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name_ar',190)->nullable();
            $table->string('name_en',190)->nullable();

            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();

            // الربط مع جداولك القائمة:
            $table->unsignedBigInteger('store_id')->nullable();     // -> store_main_date.id
            $table->unsignedBigInteger('supplier_id')->nullable();  // -> suppliers_data.id

            $table->string('serial_no',190)->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('cost',12,2)->nullable();

            // افتراضيات الإهلاك
            $table->integer('useful_life_months')->nullable();
            $table->enum('dep_method',['SL','DDB','UOP'])->nullable();
            $table->decimal('dep_rate',6,3)->nullable();

            // حالة الأصل (تكامل الصيانة لاحقًا)
            $table->enum('status',['in_service','maintenance','retired','scrapped'])->default('in_service');

            // QR / RFID
            $table->string('qr_code_path',255)->nullable();
            $table->string('rfid_tag',190)->nullable()->unique();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // علاقات الإعدادات (موجودة لديك)
            $table->foreign('category_id')->references('id')->on('asset_categories')->nullOnDelete();
            $table->foreign('brand_id')->references('id')->on('asset_brands')->nullOnDelete();
            $table->foreign('model_id')->references('id')->on('asset_models')->nullOnDelete();
            $table->foreign('location_id')->references('id')->on('asset_locations')->nullOnDelete();

            // علاقات مع جداولك الحالية
            $table->foreign('store_id')->references('id')->on('store_main_date')->nullOnDelete();
            $table->foreign('supplier_id')->references('id')->on('suppliers_data')->nullOnDelete();

            $table->index(['status','is_active']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('asset_items');
    }
};
