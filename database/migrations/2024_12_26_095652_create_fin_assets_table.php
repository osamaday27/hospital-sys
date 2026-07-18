<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_items', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar'); // اسم الأصل بالعربية
            $table->string('name_en'); // اسم الأصل بالإنجليزية
            $table->string('asset_type')->nullable(); // نوع الأصل (معدات طبية، أجهزة، أثاث...)
            $table->string('department')->nullable(); // القسم التابع له الأصل
            $table->string('serial_number')->unique()->nullable(); // الرقم التسلسلي
            $table->date('purchase_date')->nullable(); // تاريخ الشراء
            $table->decimal('purchase_price', 15, 2)->nullable(); // سعر الشراء
            $table->string('warranty_status')->nullable(); // حالة الضمان (ساري، منتهي)
            $table->date('warranty_expiration')->nullable(); // تاريخ انتهاء الضمان
            $table->string('location')->nullable(); // مكان الأصل داخل المستشفى
            $table->enum('operational_status', ['operational', 'under_maintenance', 'out_of_service'])->default('operational'); // الحالة التشغيلية
            $table->enum('status', ['active', 'inactive'])->default('active'); // حالة السجل
            $table->unsignedBigInteger('user_id')->nullable(); // معرف المستخدم الذي أنشأ الأصل
            $table->timestamps();
            $table->softDeletes(); // الحذف الناعم
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asset_items');
    }
};