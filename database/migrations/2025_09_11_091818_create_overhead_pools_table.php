<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('overhead_pools', function (Blueprint $table) {
            $table->id();

            // تعريف المجمّع
            $table->string('code', 50)->unique();
            $table->json('name'); // Spatie Translatable
            $table->enum('category', [
                'utilities',    // مرافق (كهرباء، مياه، غاز..)
                'maintenance',  // صيانة
                'administration', // إدارة عامة
                'cleaning',     // نظافة
                'rent',         // إيجار
                'it',           // تقنية معلومات
                'security',     // أمن
                'other'         // أخرى
            ])->default('other')->index();

            // فترة التغطية
            $table->date('period_start')->index();
            $table->date('period_end')->index();

            // المبلغ الإجمالي للفترة
            $table->decimal('amount', 18, 2)->default(0);

            // حالة وملاحظات
            $table->enum('status', ['draft','approved','posted'])->default('draft')->index();
            $table->text('notes')->nullable();

            // تتبع المستخدم
            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overhead_pools');
    }
};