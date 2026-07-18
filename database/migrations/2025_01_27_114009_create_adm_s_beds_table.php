<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adm_s_beds', function (Blueprint $table) {
            $table->id(); // مفتاح أساسي
            $table->string('name'); // اسم السرير
            $table->enum('type', ['general', 'icu', 'nursery'])->default('general'); // نوع السرير
            $table->unsignedBigInteger('room_id'); // الغرفة المرتبطة

            $table->foreignId('booking_id')->nullable()->constrained('adm_s_inpatient_booking')->onDelete('set null'); // معرف الحجز الحالي

            $table->enum('status', ['available', 'occupied', 'maintenance', 'reserved', 'out_of_service', 'sanitized', 'pending_cleaning', 'allocated', 'isolation'])->default('available'); // الحالة الافتراضية

            $table->unsignedBigInteger('user_id')->nullable(); // معرف المستخدم
            $table->timestamps(); // توقيتات الإنشاء والتحديث
            $table->softDeletes(); // الحذف الناعم

            // إضافة علاقة مع جدول الغرف
            $table->foreign('room_id')->references('id')->on('adm_s_rooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adm_s_beds');
    }
};