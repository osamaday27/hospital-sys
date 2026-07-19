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
        Schema::create('adm_b_surgery_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surgical_id')->constrained('fin_services')->onDelete('cascade');
            $table->foreignId('room_id')->nullable()->constrained('adm_s_rooms_surgical')->cascadeOnDelete();
            $table->text('patient_id')->nullable(); // الإصلاح هنا            $table->foreignId('operation_id')->constrained('fin_services')->cascadeOnDelete();
            $table->text('booking_date')->nullable();
            $table->text('scheduled_from')->nullable();
            $table->text('scheduled_to')->nullable();
            $table
                ->enum('status', [
                    'booked', // ✅ تم حجز العملية وتحديد الموعد
                    'confirmed', // ✅ تم تأكيد الحجز من قبل الطبيب أو الإدارة
                    'preparing_patient', // ⏳ تجهيز المريض قبل العملية
                    'preparing_room', // ⏳ تجهيز غرفة العمليات والمعدات
                    'ready_for_surgery', // 🔹 المريض جاهز للعملية في غرفة العمليات

                    'anesthesia_started', // 🛑 بدأ التخدير
                    'anesthesia_completed', // 🛑 التخدير مكتمل والمريض تحت تأثيره بالكامل
                    'incision_started', // ✂️ تم شق الجلد وبدأت العملية الجراحية
                    'in_progress', // ⚙️ العملية جارية والجراح يقوم بالإجراء
                    'on_hold', // ⏸️ تم إيقاف العملية مؤقتًا لسبب طبي

                    'surgery_completed', // ✅ العملية اكتملت بنجاح
                    'closing_wound', // ✂️ يتم إغلاق الجرح وخياطة المنطقة
                    'wound_closed', // ✅ الجرح مغلق والانتهاء من الإجراء
                    'anesthesia_reversed', // ⏳ يتم إيقاف تأثير التخدير تدريجيًا
                    'recovery_room', // 🏥 المريض في غرفة الإفاقة تحت المراقبة
                    'under_observation', // 👀 المريض تحت المراقبة الطبية بعد العملية
                    'discharged', // 🎉 خروج المريض بعد نجاح العملية

                    'canceled', // ❌ تم إلغاء العملية قبل أن تبدأ
                    'failed', // ⚠️ فشلت العملية بسبب مضاعفات أو مشاكل
                    'postponed', // ⏳ تم تأجيل العملية لموعد لاحق
                    'transferred', // 🚑 تم نقل المريض إلى مستشفى آخر
                ])
                ->default('booked'); // الحالة الافتراضية: تم الحجز

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adm_b_surgery_bookings');
    }
};
