<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierAlertsTable extends Migration
{
    public function up()
    {
        Schema::create('supplier_alerts', function (Blueprint $table) {
            $table->bigIncrements('id');

            // علاقة بالمورد
            $table->unsignedBigInteger('supplier_id');

            // نوع الإشعار: إضافة أو خصم
            $table->enum('type', ['addition', 'discount']);

            // القيمة
            $table->decimal('value', 15, 2);

            // تاريخ الإشعار
            $table->date('notify_date');

            // ملاحظات اختيارية
            $table->text('notes')->nullable();

            // تواريخ الإنشاء والتعديل والحذف اللطيف
            $table->timestamps();
            $table->softDeletes();

            // قيد المفتاح الأجنبي
            $table->foreign('supplier_id')
                  ->references('id')
                  ->on('suppliers_data')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_alerts');
    }
}
