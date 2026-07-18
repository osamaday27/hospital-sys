<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {

    // ناخد أسماء الجداول الفعلية من الموديلات لتجنّب أي لبس
    $productsTable = (new \App\Models\Supply_Chain\ProductData)->getTable();   // "products_data"
    $unitsTable    = (new \App\Models\Supply_Chain\ProductUnit)->getTable();   // "product_units"

    Schema::create('receiving_inspecting_items', function (Blueprint $table) use ($productsTable, $unitsTable) {
      $table->id();

      $table->foreignId('receiving_inspecting_id')
            ->constrained('receiving_inspecting')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();

      // حدّد جدول FK صراحة
      $table->foreignId('product_id')
            ->constrained($productsTable)
            ->cascadeOnUpdate()
            ->restrictOnDelete();

      $table->foreignId('unit_id')
            ->constrained($unitsTable)
            ->cascadeOnUpdate()
            ->restrictOnDelete();

      // الكميات
      $table->decimal('request_qty',   15, 3)->default(0);
      $table->decimal('po_qty',        15, 3)->default(0);
      $table->decimal('delivered_qty', 15, 3)->default(0);
      $table->decimal('accepted_qty',  15, 3)->default(0);
      $table->decimal('rejected_qty',  15, 3)->default(0);

      $table->decimal('cost_price', 15, 2)->nullable();
      $table->string('rejection_reason')->nullable();

      $table->timestamps();
      $table->softDeletes();

      // اسم فهرس قصير
      $table->index(['receiving_inspecting_id','product_id'], 'rii_doc_prod_idx');
    });
  }

  public function down(): void {
    Schema::dropIfExists('receiving_inspecting_items');
  }
};
