<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        Schema::create('fin_price_list_service', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('price_list_id');
            $table->unsignedBigInteger('service_id');
            $table->decimal('hospital_price', 10, 2)->default('0.00');; // سعر المستشفى
            $table->decimal('special_price', 10, 2)->default('0.00');; // السعر الخاص
            $table->enum('status', ['active', 'inactive'])->default('active'); // الحالة
            $table->unsignedBigInteger('user_id')->nullable(); // معرف المستخدم الذي أنشأ القائمة  
            $table->timestamps(); // تاريخ الإنشاء والتحديث
            $table->softDeletes(); // حذف منطقي
            // إعداد العلاقات (foreign keys)
            $table->foreign('price_list_id')->references('id')->on('fin_price_lists')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('fin_services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fin_price_list_service');
    }
};
