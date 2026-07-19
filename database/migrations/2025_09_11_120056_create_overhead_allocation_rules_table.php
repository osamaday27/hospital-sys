<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('overhead_allocation_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('overhead_pool_id')->index(); // المجمّع المعتمد
            $table->enum('method', ['percent','driver'])->default('percent')->index();
            $table->string('driver_code', 50)->nullable()->index(); // اسم/كود الـ Driver لو method = driver (مثلاً: area, staff, machine_hours)
            $table->decimal('total_amount', 18, 2)->default(0); // نسخة من مبلغ المجمّع وقت الإنشاء
            $table->decimal('total_percent', 8, 4)->default(0); // تجميعي للمراجعة
            $table->decimal('total_driver', 18, 4)->default(0); // تجميعي للمراجعة
            $table->enum('status', ['draft','approved','posted'])->default('draft')->index();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('overhead_pool_id')->references('id')->on('overhead_pools')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overhead_allocation_rules');
    }
};
