<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patient_files', function (Blueprint $table) {
            $table->id();

            // المريض والزيارة
            $table->unsignedBigInteger('patient_id')->nullable()->index();
            $table->unsignedBigInteger('visit_id')->nullable()->index();

            // بيانات الملف
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->string('file_mime')->nullable();         // النوع الكامل (MIME)
            $table->string('file_extension', 20)->nullable();// الامتداد مثل pdf, jpg
            $table->unsignedInteger('size_in_kb')->nullable();

            // نوع المستند (Prescription, Report, Lab, Radiology...)
            $table->string('document_type')->nullable();

            $table->text('notes')->nullable();

            // المستخدم الذي رفع
            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_files');
    }
};