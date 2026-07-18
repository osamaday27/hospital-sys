<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('client_store_returns', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('id');

            $table->string('code', 50)->index();              // تجميعة المستند
            $table->string('request_number', 50)->index();    // رقم طلب العميل

            // مهم: patient.id = INT UNSIGNED => استخدم unsignedInteger
            $table->unsignedInteger('client_id')->index();
            $table->foreign('client_id')
                  ->references('id')->on('patient')
                  ->onDelete('RESTRICT');

            // عدّل النوع هنا إذا لزم ليتطابق مع مفاتيحك
            $table->unsignedBigInteger('store_id')->index();
            $table->foreign('store_id')
                  ->references('id')->on('store_main_date')
                  ->onDelete('RESTRICT');

            $table->date('return_date')->index();

            $table->unsignedBigInteger('product_id')->index();
            $table->foreign('product_id')
                  ->references('id')->on('products_data')
                  ->onDelete('RESTRICT');

            $table->unsignedBigInteger('unit_id')->index();
            $table->foreign('unit_id')
                  ->references('id')->on('units')
                  ->onDelete('RESTRICT');

            $table->string('batch_no', 100)->nullable()->index();
            $table->decimal('quantity', 20, 6);

            $table->text('notes')->nullable();

            // users.id غالبًا BIGINT UNSIGNED — عدّله لو عندك مختلف
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->foreign('created_by')
                  ->references('id')->on('users')
                  ->onDelete('SET NULL');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_store_returns');
    }
};
