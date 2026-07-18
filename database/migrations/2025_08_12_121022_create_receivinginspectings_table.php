<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {

    $suppliersTable = (new \App\Models\Suppliers\SuppliersData)->getTable();
    $storesTable    = (new \App\Models\supply_chain\storemaindata)->getTable();
    $poTable        = (new \App\Models\purchases\purchaseorder)->getTable();

    Schema::create('receiving_inspecting', function (Blueprint $table) use ($suppliersTable, $storesTable, $poTable) {
      $table->id();

      // علاقات رئيسية
      $table->foreignId('supplier_id')
            ->constrained($suppliersTable)
            ->cascadeOnUpdate()
            ->restrictOnDelete();

      $table->foreignId('store_id')
            ->constrained($storesTable)
            ->cascadeOnUpdate()
            ->restrictOnDelete();

      $table->foreignId('purchase_order_id')
            ->constrained($poTable)
            ->cascadeOnUpdate()
            ->restrictOnDelete();

      // بيانات المحضر
      $table->string('code')->unique();   // يُنشأ في الكنترولر
      $table->date('document_date');

      // ملاحظات
      $table->text('notes')->nullable();
      $table->text('receipt_notes')->nullable();

      // الحالة
      $table->enum('status', ['pending','approved','rejected'])->default('pending');

      $table->timestamps();
      $table->softDeletes();

      // اسم فهرس قصير لتفادي حد الـ 64 حرف
      $table->index(['supplier_id','store_id','purchase_order_id','document_date'], 'ri_main_idx');
    });
  }

  public function down(): void {
    Schema::dropIfExists('receiving_inspecting');
  }
};
