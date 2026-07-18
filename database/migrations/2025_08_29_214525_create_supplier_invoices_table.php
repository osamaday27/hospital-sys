<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();

            // المورد
            $table->unsignedBigInteger('supplier_id');

            // الفرع (لو عندك نظام فروع في المستشفى)
            $table->unsignedBigInteger('branch_id')->nullable();

            // بيانات الفاتورة
            $table->string('invoice_no')->unique();     // رقم الفاتورة
            $table->date('invoice_date');               // تاريخ الفاتورة
            $table->date('due_date')->nullable();       // تاريخ الاستحقاق
            $table->enum('invoice_type', ['products','services','mixed'])->default('products'); // نوع الفاتورة

            // بيانات مالية
            $table->decimal('subtotal', 15, 2)->default(0);        // قبل الضريبة
            $table->decimal('discount', 15, 2)->default(0);        // الخصم
            $table->decimal('tax', 15, 2)->default(0);             // الضريبة
            $table->decimal('total_amount', 15, 2)->default(0);    // الإجمالي بعد الخصم والضريبة
            $table->decimal('paid_amount', 15, 2)->default(0);     // المدفوع
            $table->decimal('remaining_amount', 15, 2)->default(0);// المتبقي

            // حالة الفاتورة
            $table->enum('status', ['new', 'partial', 'paid', 'cancelled','approved'])->default('new');

            // تفاصيل إضافية
            $table->string('payment_method')->nullable(); // طريقة الدفع (كاش / بنك / شيك ...)
            $table->string('reference_no')->nullable();   // رقم مرجعي مثل رقم تحويل بنكي
            $table->text('notes')->nullable();            // ملاحظات

            // المستخدمين والمتابعة
            $table->unsignedBigInteger('user_id')->nullable();      // من أنشأ الفاتورة
            $table->unsignedBigInteger('approved_by')->nullable();  // من اعتمدها

            // توقيتات
            $table->timestamps();
            $table->softDeletes();

            // علاقات
            $table->foreign('supplier_id')->references('id')->on('suppliers_data')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_invoices');
    }
};