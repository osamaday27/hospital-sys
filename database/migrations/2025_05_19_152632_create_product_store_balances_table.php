<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_store_balances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained('products_data')->onDelete('cascade');
            $table->foreignId('store_id')->constrained('store_main_date')->onDelete('cascade');
            $table->foreignId('units_id')->constrained('units')->onDelete('cascade');

            $table->decimal('quantity', 12, 2)->default(0); // الكمية الرقمية حسب الوحدة
            $table->string('batch_no')->nullable(); // رقم التشغيل
            $table->date('expiry_date')->nullable(); // تاريخ الانتهاء

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['product_id', 'store_id', 'units_id', 'batch_no', 'expiry_date'], 'unique_store_balance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_store_balances');
    }
};