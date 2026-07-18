<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('patient_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id')->nullable(); // معرف المريض
            $table->foreign('patient_id')->references('id')->on('patient')->onDelete('set null'); 
            $table->unsignedInteger('visit_id')->nullable(); // معرف المريض
            $table->foreign('visit_id')->references('id')->on('patient_visits')->onDelete('set null'); 
            $table->string('step_name'); // اسم الخطوة
            $table->enum('step_status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending'); // حالة الخطوة
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null'); // المسؤول عن تسجيل الخطوة
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // الشخص المنفذ للخطوة
            $table->text('notes')->nullable(); // ملاحظات إضافية
            $table->timestamp('start_time')->nullable(); // وقت بدء الخطوة
            $table->timestamp('end_time')->nullable(); // وقت انتهاء الخطوة
            $table->timestamps();
            $table->softDeletes(); // للحذف الناعم
        });
    }

    public function down()
    {
        Schema::dropIfExists('patient_tracking');
    }
};