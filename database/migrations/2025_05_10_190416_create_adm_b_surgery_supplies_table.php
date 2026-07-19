<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('adm_b_surgery_supplies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surgery_booking_id'); // الربط مع العملية
            $table->unsignedBigInteger('supply_id'); // المستلزم
            $table->unsignedBigInteger('unit_id'); // الوحدة
            $table->integer('quantity'); // الكمية
            $table->decimal('available_stock', 10, 2)->default(0); // الرصيد المتاح وقت التسجيل
            $table->text('notes')->nullable();
            $table->string('created_by')->nullable(); // المستخدم الذي قام بإنشاء السجل
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('surgery_booking_id')->references('id')->on('adm_b_surgery_bookings')->onDelete('cascade');
            $table->foreign('supply_id')->references('id')->on('products_data')->onDelete('restrict');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('restrict');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('adm_b_surgery_supplies');
    }
};
