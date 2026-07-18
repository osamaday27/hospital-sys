<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // أسماء الجداول الفعلية من الموديلات لتجنّب الالتباس
        $patientsTable  = (new \App\models\administrative\medical_record)->getTable(); // "patient"
        $employeesTable = (new \App\models\hr\Employee)->getTable();                  // غالباً "employees"
        $usersTable     = (new \App\Models\User)->getTable();                         // غالباً "users"

        Schema::create('patient_inquiries', function (Blueprint $table) use ($patientsTable, $employeesTable, $usersTable) {
            $table->id();

            // هل صاحب الاستفسار مريض مسجّل؟ (إجباري)
            $table->boolean('is_registered');

            // لو مسجّل: ربط اختياري بسجل المريض (جدول patient عندك مفتاحه int)
            $table->unsignedInteger('patient_id')->nullable();
            $table->foreign('patient_id')
                  ->references('id')->on($patientsTable)
                  ->nullOnDelete();

            // بيانات تعريفية للحالات غير المسجّلة (كلها اختيارية)
            $table->string('full_name', 150)->nullable();
            $table->enum('gender', ['male','female'])->nullable();
            $table->unsignedSmallInteger('age')->nullable();

            // تاريخ الاستفسار (اختياري)
            $table->date('inquiry_date')->nullable();

            // وسائل اتصال متعددة (اختياري) - بصيغة JSON
            // مثال: [{"type":"phone","value":"0100..."},{"type":"whatsapp","value":"..."}]
            $table->json('contact_methods')->nullable();

            // نص الاستفسار (إجباري)
            $table->text('inquiry');

            // الحالة (افتراضي new) والقيمة الأخرى reviewed
            $table->enum('status', ['new','reviewed'])->default('new');

            // الطبيب المستفسَر عنه (اختياري) - ربط بجدول الموظفين/الأطباء
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->foreign('doctor_id')
                  ->references('id')->on($employeesTable)
                  ->nullOnDelete();

            // جهة التحويل + من أين تعرف علينا (اختياريان)
            $table->string('referral_entity')->nullable();
            $table->string('heard_about_us')->nullable();

            // ملاحظات (اختياري)
            $table->text('notes')->nullable();

            // المستخدم المنشئ (اختياري) - ربط بجدول المستخدمين
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')
                  ->references('id')->on($usersTable)
                  ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // فهارس مفيدة
            $table->index(['patient_id', 'status']);
            $table->index('inquiry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_inquiries');
    }
};
