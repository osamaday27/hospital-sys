<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('medication_takens', function (Blueprint $table) {
            if (!Schema::hasColumn('medication_takens', 'description')) {
                // تم إزالة ->after('name') لتجنب الخطأ
                $table->json('description')->nullable();
            }

            if (!Schema::hasColumn('medication_takens', 'is_active')) {
                // تم إزالة ->after('description') لضمان المرونة
                $table->boolean('is_active')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('medication_takens', function (Blueprint $table) {
            if (Schema::hasColumn('medication_takens', 'description')) {
                $table->dropColumn('description'); //
            }

            if (Schema::hasColumn('medication_takens', 'is_active')) {
                $table->dropColumn('is_active'); //
            }
        });
    }
};
