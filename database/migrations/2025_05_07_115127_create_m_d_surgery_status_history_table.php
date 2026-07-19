<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('adm_b_surgery_status_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surgery_booking_id'); // ربط بالحجز
            $table->enum('status', ['confirmed', 'postponed', 'cancelled'])->default('confirmed');
            $table->text('note')->nullable(); // ملاحظة اختيارية لكل حالة
            $table->timestamp('status_changed_at')->nullable(); // تاريخ التغيير
            $table->unsignedBigInteger('changed_by')->nullable(); // المستخدم الذي غيّر الحالة
            $table->timestamps();

            // إضافة قيود العلاقات
            $table->foreign('surgery_booking_id')->references('id')->on('adm_b_surgery_bookings')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adm_b_surgery_status_history');
    }
};
