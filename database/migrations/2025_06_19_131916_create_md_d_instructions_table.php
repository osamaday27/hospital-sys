<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('md_d_instructions', function (Blueprint $table) {
            $table->id();

            // علاقات
            // هيربط تلقائياً patient_id بجدول patients.id بنوع صحيح
            // حقول الربط (INT UNSIGNED)
            $table->unsignedInteger('patient_id');
            $table->unsignedInteger('visit_id')->nullable();
            $table->unsignedInteger('reservation_id')->nullable();
            $table->foreignId('doctor_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('nurse_id')
                  ->nullable()
                  ->constrained('users')
                  ->cascadeOnDelete();
            // محتوى التعليمة
            $table->text('instruction');
            $table->enum('importance', ['low', 'medium', 'high'])->default('medium');

            // الحالة وتتبع التنفيذ
            $table->enum('status', ['pending', 'in_progress', 'finished', 'cancelled'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // زمنية وجدولة
            $table->boolean('time_specific')->default(false);
            $table->timestamp('scheduled_at')->nullable();
            $table->unsignedInteger('repeat_count')->default(1);
            $table->string('repeat_interval')->nullable(); //مثلاً "4 hours", "1 day"
            $table->enum('on_repeat_action', ['duplicate', 'remind', 'skip'])->default('duplicate');

            // ملاحظات الممرّض
            $table->text('notes')->nullable();
            // قيود المفاتيح الخارجية
            $table->foreign('patient_id')->references('id')->on('patient')->cascadeOnDelete();

            $table->foreign('visit_id')->references('id')->on('patient_visits')->cascadeOnDelete();

            $table->foreign('reservation_id')->references('id')->on('adm_s_reservations')->cascadeOnDelete();

         
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('md_d_instructions');
    }
};