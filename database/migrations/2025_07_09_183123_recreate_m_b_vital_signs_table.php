<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // لو الجدول موجود امسحه
        Schema::dropIfExists('m_b_vital_signs');

        // إنشاء الجدول من جديد
        Schema::create('m_b_vital_signs', function (Blueprint $table) {
            $table->id();
            $table->text('id_patient')->nullable();
            $table->text('id_visit')->nullable();
            $table->text('id_reservation')->nullable();
            $table->text('type_visit')->nullable();
            $table->text('temperature')->nullable();
            $table->text('heart_rate')->nullable();
            $table->text('respiratory_rate')->nullable();
            $table->text('systolic_bp')->nullable();
            $table->text('diastolic_bp')->nullable();
            $table->text('oxygen_saturation')->nullable();
            $table->text('blood_sugar')->nullable();
            $table->text('weight')->nullable();
            $table->text('height')->nullable();
            $table->text('bmi')->nullable();
            $table->text('pain_scale')->nullable();
            $table->text('notes')->nullable();
            $table->text('status')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_b_vital_signs');
    }
};