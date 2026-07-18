<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rad_s_devicestypes', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // متعدد اللغات
            $table->json('description')->nullable(); // وصف متعدد اللغات
            $table->enum('type', ['X-Ray','Digital X-Ray','MRI','CT','Ultrasound','Mammography','Nuclear Medicine']);
            $table->enum('category', ['diagnostic','therapeutic']); // تصنيف حسب الاستخدام
            $table->enum('status', ['active','inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rad_s_devicestypes');
    }
};
