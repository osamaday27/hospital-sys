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
        Schema::create('m_d_request_medical_examination', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id')->nullable(); // معرف المريض
            $table->foreign('patient_id')->references('id')->on('patient')->onDelete('set null');
            $table->unsignedInteger('visit_id')->nullable(); // معرف المريض
            $table->foreign('visit_id')->references('id')->on('patient_visits')->onDelete('set null');
            $table->unsignedBigInteger('requested_by'); // المستخدم الطالب
            $table->foreignId('service_id')->constrained('fin_services')->nullable(); // الخدمات من جدول الخدمات// الفحص المطلوب
            $table->string('eye'); // العين المطلوب فحصها
            $table->text('preliminary_diagnosis')->nullable(); // التشخيص المبدئي
            $table->text('clinical_info')->nullable(); // معلومات إكلينيكية
            $table->enum('test_type', ['urgent', 'routine'])->default('routine'); // نوع الفحص
            $table->text('status');
            $table->timestamps();
            $table->softDeletes(); // للحذف الناعم
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_d_request_medical_examination');
    }
};
