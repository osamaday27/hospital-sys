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
        Schema::create('adm_b_surgery_medical_team', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('surgery_bookings_id')->constrained('adm_b_surgery_bookings')->onDelete('cascade'); // معرف الفريق الطبي
            $table->foreignId('medical_team_id')->constrained('employees')->onDelete('cascade'); // معرف الفريق الطبي
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // معرف الطبيب أو الممرض
            $table->enum('role', ['surgeon', 'assistant_surgeon', 'anesthesiologist', 'nurse', 'technician', 'other']); // دور العضو
            $table
                ->enum('status', [
                    'assigned', // ✅ تم تعيين الفريق
                    'ready', // 🟢 الفريق جاهز
                    'in_surgery', // ⚙️ الفريق يعمل في العملية الآن
                    'completed', // ✅ الفريق أكمل العملية
                    'on_hold', // ⏸️ الفريق متوقف مؤقتًا
                    'canceled', // ❌ تم إلغاء مشاركة الفريق
                ])
                ->default('assigned'); // الحالة الافتراضية: تم تعيين الفريق
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adm_b_surgery_medical_team');
    }
};
