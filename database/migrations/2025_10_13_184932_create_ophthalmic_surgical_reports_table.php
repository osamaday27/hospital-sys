<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ophthalmic_surgical_reports', function (Blueprint $table) {
            $table->id();

            // 🔗 العلاقات الأساسية
            $table->unsignedInteger('patient_id')->nullable();
            $table->unsignedInteger('visit_id')->nullable();
            $table->foreignId('booking_id')->nullable()->constrained('adm_b_surgery_bookings')->nullOnDelete();
            $table->unsignedBigInteger('doctor_id')->nullable(); // employees.id = bigint unsigned

            // 🩺 بيانات عامة لكل العمليات
            $table->string('report_type')->nullable(); // مثال: ICL / Keratoplasty / OCT / Vitrectomy ...
            $table->date('surgery_date')->nullable();
            $table->string('eye')->nullable(); // OD / OS / OU
            $table->string('anesthesia')->nullable();
            $table->string('surgeon')->nullable();
            $table->string('preoperative_diagnosis')->nullable();
            $table->string('postoperative_diagnosis')->nullable();
            $table->text('operative_details')->nullable();
            $table->text('surgical_procedure')->nullable();
            $table->text('complications')->nullable();
            $table->text('postoperative_care')->nullable();

            // 🧠 متغيرات إضافية للأنواع الخاصة
            $table->string('technique')->nullable(); // مثال: Phakoemulsification / Trabeculectomy / Cross Linking
            $table->string('instrumentation')->nullable(); // أمثلة: 23G, 25G, keratome size ...
            $table->string('materials_used')->nullable(); // Mitomycin, Healon, IOL type...
            $table->string('tamponade_agent')->nullable(); // Gas / Silicone oil / Air ...
            $table->string('graft_size')->nullable(); // For Keratoplasty
            $table->string('iridectomy_type')->nullable();
            $table->string('laser_settings')->nullable(); // For Glaucoma or Cyclophotocoagulation
            $table->text('findings')->nullable(); // For OCT or special findings
            $table->text('comments')->nullable();

            // 📄 ملاحظات عامة
            $table->text('general_statement')->nullable(); // النص القياسي المشترك قبل العملية
            $table->string('status')->default('active'); // active / archived / draft
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // ⚙️ علاقات خارجية
          
            $table->foreign('patient_id')
                ->references('id')
                ->on('patient')
                ->onDelete('cascade');
                
            $table->foreign('visit_id')
                ->references('id')
                ->on('patient_visits')
                ->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('employees');
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ophthalmic_surgical_reports');
    }
};
