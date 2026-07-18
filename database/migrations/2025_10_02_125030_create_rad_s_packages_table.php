<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rad_s_packages', function (Blueprint $table) {
            $table->id();

            // 🏷️ الاسم (متعدد اللغات)
            $table->json('name');

            // 📄 الوصف (متعدد اللغات)
            $table->json('description')->nullable();

            // 🧩 الخدمات (ممكن تكون IDs لخدمات تانية)
            $table->json('services')->nullable();

            // ⏳ المدة (عدد الأيام)
            $table->integer('duration_days')->nullable();

            // 🎂 العمر (حد أدنى أو مدى عمري للباقات الطبية)
            $table->integer('age_from')->nullable();
            $table->integer('age_to')->nullable();

            // 👤 الجنس (male / female / both)
            $table->enum('gender', ['male', 'female', 'both'])->default('both');

            // 🟢 الحالة (تفعيل/إيقاف)
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rad_s_packages');
    }
};
