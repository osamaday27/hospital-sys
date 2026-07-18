<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('medication_durations', function (Blueprint $table) {
            if (!Schema::hasColumn('medication_durations', 'days')) {
                $table->integer('days')->nullable()->after('name');
            }

            if (!Schema::hasColumn('medication_durations', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('days');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medication_durations', function (Blueprint $table) {
            if (Schema::hasColumn('medication_durations', 'days')) {
                $table->dropColumn('days');
            }

            if (Schema::hasColumn('medication_durations', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
