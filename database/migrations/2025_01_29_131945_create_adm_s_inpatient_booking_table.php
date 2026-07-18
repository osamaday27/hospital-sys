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
        Schema::create('adm_s_inpatient_booking', function (Blueprint $table) {
            $table->id(); // المعرف الأساسي للحجز
            $table->unsignedInteger('patient_id')->nullable(); // معرف المريض
            $table->foreign('patient_id')->references('id')->on('patient')->onDelete('set null'); // معرف المريض المرتبط بالحجز
            $table->foreignId('bed_id')->nullable()->constrained('adm_s_beds'); // معرف السرير المحجوز
            $table->enum('patient_status', ['stable', 'critical', 'recovering'])->nullable(); // حالة المريض
            $table->string('case_type')->nullable(); // نوع الحالة المرضية
            $table->string('case_severity')->nullable(); // درجة خطورة الحالة
            $table->date('booking_date')->nullable(); // تاريخ الحجز
            $table->date('expected_discharge_date')->nullable(); // التاريخ المتوقع للخروج
            $table->foreignId('admission_source_id')->nullable()->constrained('adm_s_admission_reasons'); // مصدر دخول المريض
            $table->foreignId('referring_physician_id')->nullable()->constrained('employees'); // معرف الطبيب المحول
            $table->foreignId('treating_physician_id')->nullable()->constrained('employees'); // معرف الطبيب المعالج
            $table->string('referred_hospital')->nullable(); // المستشفى المحول منها
            $table->text('referring_physician_notes')->nullable(); // ملاحظات الطبيب المحول
            $table->text('specialty_notes')->nullable(); // ملاحظات خاصة بالتخصص
            $table->foreignId('room_id')->nullable()->constrained('adm_s_rooms'); // معرف الغرفة المحجوزة
            $table->foreignId('nursing_station_id')->nullable()->constrained('adm_s_nursing_stations'); // محطة التمريض المرتبطة بالحجز
            $table->boolean('full_room_booking')->default(false)->nullable(); // حجز الغرفة بالكامل أم لا
            $table->string('residency_level')->nullable(); // درجة الإقامة
            $table->boolean('blood_donation')->default(false)->nullable(); // هل تم التبرع بالدم أم لا
            $table->string('admission_reason')->nullable(); // سبب الدخول للمستشفى
            $table->string('manual_booking_number')->nullable(); // الرقم اليدوي للحجز
            $table->text('booking_notes')->nullable(); // ملاحظات الحجز
            $table->text('initial_diagnosis')->nullable(); // التشخيص المبدئي
            $table->text('physician_request_notes')->nullable();
            $table
                ->enum('status', ['pending', 'confirmed', 'canceled', 'admitted', 'discharged', 'postponed', 'rejected'])
                ->nullable()
                ->comment('حالة الحجز: pending - قيد الانتظار, confirmed - مؤكد, canceled - ملغي, admitted - تم الإدخال, discharged - تم الخروج, postponed - مؤجل, rejected - مرفوض');

            // ملاحظات الطبيب حول الطلب
            $table->unsignedInteger('visit_id')->nullable(); // معرف المريض
            $table->foreign('visit_id')->references('id')->on('patient_visits')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users'); // المستخدم الذي أنشأ الحجز
            $table->softDeletes(); // الحذف الناعم للحجوزات
            $table->timestamps(); //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adm_s_inpatient_booking');
    }
};