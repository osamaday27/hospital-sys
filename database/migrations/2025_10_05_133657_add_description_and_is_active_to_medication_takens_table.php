<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('medication_takens', function (Blueprint $table) {
            if (!Schema::hasColumn('medication_takens', 'description')) {
                $table->json('description')->nullable()->after('name');
            }

            if (!Schema::hasColumn('medication_takens', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medication_takens', function (Blueprint $table) {
            if (Schema::hasColumn('medication_takens', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('medication_takens', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
