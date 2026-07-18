<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_store_returns', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();

            // معلومات المستند
            $table->string('code', 50)->index();
            $table->string('request_number', 50)->index();

            // الرأس
            $table->foreignId('employee_id')  // BIGINT UNSIGNED + FK
                  ->constrained('employees')
                  ->restrictOnDelete()->cascadeOnUpdate();

            $table->foreignId('store_id')
                  ->constrained('store_main_date')
                  ->restrictOnDelete()->cascadeOnUpdate();

            $table->date('return_date')->index();

            // تفاصيل الصنف
            $table->foreignId('product_id')
                  ->constrained('products_data')
                  ->restrictOnDelete()->cascadeOnUpdate();

            $table->foreignId('unit_id')
                  ->constrained('units')
                  ->restrictOnDelete()->cascadeOnUpdate();

            $table->string('batch_no', 100)->nullable()->index();
            $table->decimal('quantity', 18, 4);

            // أخرى
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()
                  ->constrained('users')
                  ->nullOnDelete()->cascadeOnUpdate();

            $table->timestamps();
            $table->softDeletes();

            // فهارس مفيدة
            $table->index(['code', 'product_id']);
            $table->index(['store_id', 'product_id', 'batch_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_store_returns');
    }
};
