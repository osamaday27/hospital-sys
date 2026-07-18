<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('m_b_vital_signs', function (Blueprint $table) {
            $table->id();
            $table->string('id_patient')->nullable();
            $table->string('id_visit')->nullable();
            $table->string('id_reservation')->nullable();
            $table->string('type_visit')->nullable();
            $table->string('temperature', 5, 2); // درجة الحرارة
            $table->string('heart_rate'); // معدل نبضات القلب (bpm)
            $table->string('respiratory_rate'); // معدل التنفس (breaths per minute)
            $table->string('systolic_bp'); // ضغط الدم الانقباضي
            $table->string('diastolic_bp'); // ضغط الدم الانبساطي
            $table->string('oxygen_saturation'); // نسبة الأكسجين (%)
            $table->decimal('blood_sugar', 5, 2)->nullable(); // نسبة السكر في الدم (mg/dL)
            $table->decimal('weight', 6, 2)->nullable(); // الوزن (kg)
            $table->decimal('height', 5, 2)->nullable(); // الطول (cm)
            $table->decimal('bmi', 5, 2)->nullable(); // مؤشر كتلة الجسم (BMI)
            $table->decimal('pain_scale', 3, 1)->nullable(); // مقياس الألم (0-10)
            $table->text('notes')->nullable(); // ملاحظات إضافية
            $table->string('status')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // الطبيب المسجل
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_b_vital_signs');
    }
};