<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rad_s_medicalreports', function (Blueprint $table) {
            $table->id();

            // العنوان متعدد اللغات
            $table->json('title');

            // محتوى القالب (HTML أو نص)
            $table->longText('template');

            // علاقات
            $table->unsignedBigInteger('device_type_id')->nullable(); // ربط بنوع الجهاز
            $table->unsignedBigInteger('specialty_id')->nullable();   // ربط بالتخصص

            // تعليمات إضافية (متعدد اللغات)
            $table->json('instructions')->nullable();

            // الحالة
            $table->string('status')->default('active');

            // توقيع الطبيب الإلكتروني (ملف أو base64 path)
            $table->string('doctor_signature')->nullable();

            // خيارات المخرجات
            $table->boolean('allow_print')->default(true);
            $table->boolean('allow_pdf')->default(true);
            $table->boolean('allow_portal')->default(true);
            $table->boolean('allow_email')->default(true);

            $table->timestamps();
            $table->softDeletes();

           $table->foreign('device_type_id')
      ->references('id')->on('rad_s_devicestypes')
      ->onDelete('set null');

$table->foreign('specialty_id')
      ->references('id')->on('specialties')
      ->onDelete('set null');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rad_s_medicalreports');
    }
};
