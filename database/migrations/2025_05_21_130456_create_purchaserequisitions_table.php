<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequisitionsTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('requisition_number')->unique();
            $table->date('requisition_date');

            // ربط بجدول المخازن الرئيسي (storemaindata → store_main_date)
            $table->foreignId('warehouse_id')
                ->constrained('store_main_date')
                ->onDelete('cascade');

            // ربط بجدول الأقسام
            $table->foreignId('department_id')
                ->constrained('hr_department')
                ->onDelete('cascade');

            // ربط بجدول الموردين (suppliersdata → suppliersdatas)
            $table->foreignId('preferred_supplier_id')
                ->nullable()
                ->constrained('suppliers_data')
                ->onDelete('set null');

            $table->text('reason')->nullable();
            $table->json('attachments')->nullable();
            $table->enum('status', [
                'pending',               // قيد الانتظار
                'receiving_quotes',      // يتم تلقي عروض الأسعار
                'order_created',         // تم إنشاء أمر الشراء
                'closed'                 // مغلق - تم التوريد
            ])->default('pending');


            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_requisitions');
    }
}
