<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('medication_units', function (Blueprint $table) {
            if (!Schema::hasColumn('medication_units', 'abbreviation')) {
                $table->json('abbreviation')->nullable()->after('name');
            }

            if (!Schema::hasColumn('medication_units', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('abbreviation');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medication_units', function (Blueprint $table) {
            if (Schema::hasColumn('medication_units', 'abbreviation')) {
                $table->dropColumn('abbreviation');
            }

            if (Schema::hasColumn('medication_units', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
