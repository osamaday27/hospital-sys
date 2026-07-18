<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supplier_returns', function (Blueprint $table) {
            $table->id();

            $table->string('code', 50)->unique();                 // كود الارتجاع
            $table->unsignedBigInteger('store_id');               // المخزن
            $table->unsignedBigInteger('supplier_id');            // المورد
            $table->string('reason')->nullable();                 // السبب

            // المجاميع
            $table->decimal('subtotal', 18, 4)->default(0);       // الإجمالي قبل الضريبة
            $table->decimal('tax_total', 18, 4)->default(0);      // إجمالي الضريبة
            $table->decimal('grand_total', 18, 4)->default(0);    // الإجمالي شامل الضريبة

            $table->date('document_date');                        // التاريخ

            $table->timestamps();
            $table->softDeletes();

            // العلاقات
            $table->foreign('store_id')
                  ->references('id')->on('store_main_date')
                  ->restrictOnDelete()->cascadeOnUpdate();

            $table->foreign('supplier_id')
                  ->references('id')->on('suppliers_data')
                  ->restrictOnDelete()->cascadeOnUpdate();

            // فهارس مساعدة
            $table->index(['document_date']);
            $table->index(['store_id', 'supplier_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_returns');
    }
};
