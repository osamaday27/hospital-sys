<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cost_adjustments', function (Blueprint $table) {
            $table->id();

            // ربط اختياري بدفعة/فترة إقفال
            $table->unsignedBigInteger('batch_id')->nullable()->index();

            // هدف التسوية (حالة/خدمة/مركز)
            $table->enum('case_type', ['opd','inpatient','surgery','lab','radiology','other'])->default('other')->index();
            $table->unsignedBigInteger('case_id')->nullable()->index();

            $table->unsignedBigInteger('service_id')->nullable()->index();
            $table->unsignedBigInteger('cost_center_id')->nullable()->index();

            // نوع السطر
            $table->enum('line_type', ['direct','overhead'])->default('direct')->index();
            $table->enum('item_type', ['supply','drug','resource','misc','overhead'])->default('misc')->index();

            // طبيعة التسوية
            $table->enum('adj_type', ['increase','decrease'])->default('increase')->index();
            $table->decimal('amount', 18, 6)->default(0);

            // بيانات مساعدة
            $table->date('effective_date')->nullable()->index();
            $table->string('reference', 100)->nullable();
            $table->text('reason')->nullable();

            // الحالة
            $table->enum('status', ['draft','approved','posted'])->default('draft')->index();

            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['batch_id','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_adjustments');
    }
};