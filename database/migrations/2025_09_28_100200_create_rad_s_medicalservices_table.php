<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rad_s_medicalservices', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // اسم الخدمة (عربي/إنجليزي)
            $table->json('description')->nullable(); // وصف الخدمة
            $table->unsignedBigInteger('device_id')->nullable(); // الجهاز المرتبط
            $table->json('instructions')->nullable(); // تعليمات (عربي/إنجليزي)
            $table->string('icd10_code')->nullable(); // كود ICD-10
            $table->string('cpt_code')->nullable(); // كود CPT
            $table->json('attachments')->nullable(); // ملفات مرفقة PDF/صور
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('device_id')->references('id')->on('rad_s_devices')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rad_s_medicalservices');
    }
};
