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

        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->onDelete('cascade'); // ربط بالوصفة
            $table->foreignId('medication_id')->constrained('medications')->onDelete('cascade'); // ربط بالدواء
            $table->integer('quantity'); // الكمية
            $table->foreignId('unit_id')->constrained('medication_units')->onDelete('cascade'); // وحدة القياس
            $table->foreignId('duration_id')->constrained('medication_durations')->onDelete('cascade'); // مدة الاستخدام
            $table->foreignId('taken_id')->constrained('medication_takens')->onDelete('cascade'); // طريقة الإعطاء
            $table->integer('timer')->nullable(); // الوقت بين الجرعات
            $table->enum('eye', ['right', 'left', 'both'])->default('both'); // العين المستهدفة
            $table->text('notes')->nullable(); // ملاحظات الطبيب
            $table->timestamps();
        });
    }

    /**
     * التراجع عن الميجريشن
     */
    public function down()
    {
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
    }
};