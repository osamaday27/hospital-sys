<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    public function up()
    {
        // 🔐 الحل الجذري الشامل: تعطيل فحص العلاقات مؤقتاً لتمرير بناء الجدول بأمان وتخطي أخطاء الترتيب
        Schema::disableForeignKeyConstraints();

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();

            // 💡 التصحيح الاحترافي: تحويل العلاقات المتشابكة إلى حقول رقمية آمنة ومطابقة تمنع انهيار MySQL
            $table->unsignedBigInteger('quotation_id');
            $table->unsignedBigInteger('requisition_id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('created_by');

            $table->string('order_number')->unique();
            $table->date('order_date');

            // العملة
            $table->string('currency', 3);

            // فترة التسليم بالأيام
            $table->integer('delivery_period')->nullable();

            // أولوية الطلب
            $table->enum('priority', ['low','normal','high','urgent'])
                  ->default('normal');

            // معلومات إضافية
            $table->string('delivery_location')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('payment_terms')->nullable();
            $table->text('notes')->nullable();

            // مجاميع الحسابات
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);
            $table->decimal('total_amount_including_tax', 15, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();

            // 🛠️ ربط العلاقات بطريقة صريحة ومستقرة تتخطى فحص الترتيب المزعج لمحرك قاعدة البيانات
            $table->foreign('quotation_id')->references('id')->on('price_quotations')->onDelete('cascade');
            $table->foreign('requisition_id')->references('id')->on('purchase_requisitions')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers_data')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
        });

        // 🔐 إعادة تفعيل فحص العلاقات فوراً بعد الانتهاء
        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('purchase_orders');
        Schema::enableForeignKeyConstraints();
    }
}
