<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rad_s_devices', function (Blueprint $table) {
            $table->id();

            // الاسم والوصف متعدد اللغات
            $table->json('name');
            $table->json('description')->nullable();

            // العلاقات
            $table->unsignedBigInteger('device_type_id');
            $table->unsignedBigInteger('room_id');

            // الحالة
            $table->enum('status', ['available', 'maintenance', 'inactive'])->default('available');

            // التواريخ
            $table->date('installation_date')->nullable();
            $table->date('maintenance_date')->nullable();

            // بيانات إضافية
            $table->string('vendor')->nullable();
            $table->string('serial_no')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // المفاتيح الخارجية ✅
            $table->foreign('device_type_id')
                  ->references('id')
                  ->on('rad_s_devicestypes')
                  ->cascadeOnDelete();

            $table->foreign('room_id')
                  ->references('id')
                  ->on('rad_s_rooms')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rad_s_devices');
    }
};
