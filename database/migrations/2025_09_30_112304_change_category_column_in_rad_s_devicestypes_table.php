<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ⚠️ مهم: لازم الأول تحذف العمود enum القديم
        Schema::table('rad_s_devicestypes', function (Blueprint $table) {
            $table->dropColumn('category');
        });

        // ✨ بعدين تضيفه كـ JSON
        Schema::table('rad_s_devicestypes', function (Blueprint $table) {
            $table->json('category')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        // ❌ لو رجعت هيرجع تاني enum
        Schema::table('rad_s_devicestypes', function (Blueprint $table) {
            $table->dropColumn('category');
        });

        Schema::table('rad_s_devicestypes', function (Blueprint $table) {
            $table->enum('category', ['diagnostic', 'therapeutic'])->default('diagnostic');
        });
    }
};
