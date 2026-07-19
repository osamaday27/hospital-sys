<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * تشغيل الميجريشن
     */
    public function up()
    {
        // 🔐 الحل الجذري الشامل: تعطيل فحص العلاقات مؤقتاً لتمرير بناء الجداول بأمان وتخطي أخطاء الترتيب
        Schema::disableForeignKeyConstraints();

        // 1. إنشاء جدول الوصفات الطبية (Prescriptions)
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_patient');
            $table->unsignedBigInteger('id_visit');
            $table->unsignedBigInteger('id_reservation');
            $table->string('type_visit');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null'); // الطبيب الذي وصف الدواء
            $table->date('prescription_date')->default(now()); // تاريخ الوصفة
            $table->enum('status', ['pending', 'completed', 'canceled'])->default('pending'); // حالة الوصفة
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. إنشاء جدول عناصر الوصفة (Prescription Items)
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->onDelete('cascade'); // ربط بالوصفة
            
            // 💡 التصحيح الاحترافي: تحويل العلاقات المتشابكة إلى حقول رقمية آمنة ومطابقة تمنع انهيار MySQL
            $table->unsignedBigInteger('medication_id');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('duration_id')->nullable();
            $table->unsignedBigInteger('taken_id')->nullable();
            
            $table->integer('quantity'); // الكمية
            $table->integer('timer')->nullable(); // الوقت بين الجرعات
            $table->enum('eye', ['right', 'left', 'both'])->default('both'); // العين المستهدفة
            $table->text('notes')->nullable(); // ملاحظات الطبيب
            $table->timestamps();

            // 🛠️ ربط العلاقات بطريقة صريحة ومستقرة تتخطى فحص الترتيب المزعج لمحرك قاعدة البيانات
            $table->foreign('medication_id')->references('id')->on('medications')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('medication_units')->onDelete('cascade');
            $table->foreign('duration_id')->references('id')->on('medication_durations')->onDelete('cascade');
            $table->foreign('taken_id')->references('id')->on('medication_takens')->onDelete('cascade');
        });

        // 🔐 إعادة تفعيل فحص العلاقات فوراً بعد الانتهاء
        Schema::enableForeignKeyConstraints();
    }

    /**
     * التراجع عن الميجريشن
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
        Schema::enableForeignKeyConstraints();
    }
};
