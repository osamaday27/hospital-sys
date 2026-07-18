<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cost_transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('batch_id')->index();   // FK لجدول الدُفعة

            // تعريف الحالة (case) التي تم تحميل التكلفة عليها
            $table->enum('case_type', ['opd','inpatient','surgery','lab','radiology','other'])->index();
            $table->unsignedBigInteger('case_id')->nullable()->index();   // رقم الزيارة/العملية/… (اختياري)

            // ربط اختياري بالخدمة ومركز التكلفة
            $table->unsignedBigInteger('service_id')->nullable()->index();
            $table->unsignedBigInteger('cost_center_id')->nullable()->index();

            // نوع الخط
            $table->enum('line_type', ['direct','overhead'])->index();
            $table->enum('item_type', ['supply','drug','resource','misc','overhead'])->nullable()->index();

            $table->string('description')->nullable();

            $table->decimal('qty', 18, 6)->default(0);
            $table->decimal('unit_cost', 18, 6)->default(0);
            $table->decimal('total_cost', 18, 6)->default(0);

            $table->json('meta')->nullable(); // بيانات إضافية (device minutes, driver share, …)

            $table->timestamps();

            $table->index(['batch_id','line_type']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('cost_transactions');
    }
};