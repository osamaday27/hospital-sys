<?php
// database/migrations/2025_08_09_000002_create_adm_b_surgery_supply_issue_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
       Schema::create('adm_b_surgery_supply_issue_items', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('issue_id'); // FK للهيدر
    $table->unsignedBigInteger('product_id');
    $table->unsignedBigInteger('unit_id');
    $table->decimal('quantity_requested', 18, 4)->default(0);
    $table->decimal('quantity_issued', 18, 4)->default(0);
    $table->decimal('quantity_in_base_unit', 18, 4)->default(0);
    $table->string('batch_no')->nullable();
    $table->date('expiry_date')->nullable();
    $table->timestamps();

    // نسمّي الـ index يدويًا باسم قصير
    $table->index(['issue_id', 'product_id', 'unit_id'], 'supply_issue_item_idx');
});

    }

    public function down(): void
    {
        Schema::dropIfExists('adm_b_surgery_supply_issue_items');
    }
};
