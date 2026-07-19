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
        Schema::create('adm_s_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الغرفة
            $table->enum('type', ['office', 'patient_room', 'operation_room', 'icu', 'emergency_room', 'delivery_room', 'waiting_room', 'isolation_room', 'laboratory', 'xray_room', 'pharmacy', 'storage', 'consultation_room', 'recovery_room', 'nurse_station'])->default('patient_room'); // القيمة الافتراضية
            $table->foreignId('floor_id')->nullable()->constrained('adm_s_floors')->onDelete('cascade'); // ارتباط بالدور
            $table->foreignId('nursing_stations_id')->nullable()->constrained('adm_s_nursing_stations')->onDelete('cascade'); // محطة التمريض
            $table->foreignId('residency_id')->nullable()->constrained('adm_s_residencies')->onDelete('set null'); // درجة الإقامة
            $table->enum('status', ['available', 'occupied', 'under_maintenance', 'reserved', 'out_of_service', 'sanitized', 'closed', 'pending_cleaning', 'allocated', 'isolation'])->default('available'); // الحالة الافتراضية

            $table->unsignedBigInteger('user_id')->nullable(); // المستخدم الذي أنشأ الغرفة

            $table->timestamps();
            $table->softDeletes(); // الحذف الناعم
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adm_s_rooms');
    }
};
