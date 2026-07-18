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
    Schema::create('products_data', function (Blueprint $table) {
        $table->id();
        // كود النظام للصنف (اختياري)
        $table->string('system_code')->nullable();
        // الاسم (العربي مطلوب)
        $table->string('name_ar');
        // الاسم (الإنجليزي مطلوب)
        $table->string('name_en');
        // مجموعة الصنف يتم جلبها من جدول مجموعات الاصناف (product_categories)
        $table->unsignedBigInteger('product_category_id')->nullable();
        // الحالة (نشط/غير نشط) - تخزينها كنمط enum
        $table->enum('status', ['active', 'inactive'])->nullable();
        // الوصف (اختياري)
        $table->text('description')->nullable();
        // الباركود (اختياري)
        $table->string('barcode')->nullable();
        // كود المنتج (اختياري)
        $table->string('product_code')->nullable();
        // الكود الموحد (اختياري)
        $table->string('unified_code')->nullable();
        // تاريخ انتهاء الصلاحية (اختياري)
        $table->date('expiration_date')->nullable();
        // هامش الربح (يمكن تخزينه كنسبة مئوية أو قيمة) – سنستخدم رقم عشري
        $table->decimal('profit_margin', 8, 2)->nullable();
        // ضريبة القيمة المضافة: تخزينها 1 او 0، وإذا كانت 1 يتم إدخال القيمة في حقل خاص
        $table->boolean('vat')->nullable();
        $table->decimal('vat_value', 8, 2)->nullable();
        // نوع الصنف (local, imported) - باللغة الإنجليزية
        $table->enum('product_type', ['local', 'imported'])->nullable();
        // الرقم الدفتري (اختياري)
        $table->string('ledger_number')->nullable();
        $table->json('images')->nullable();
        $table->timestamps();
        $table->softDeletes();

         $table->foreign('product_category_id')->references('id')->on('product_categories')->onDelete('set null');
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productdatas');
    }
};
