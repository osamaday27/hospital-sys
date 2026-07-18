<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hr_appraisal_terms_conf', function (Blueprint $table) {
            $table->decimal('percentage', 5, 2)->default(0)->after('term_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hr_appraisal_terms_conf', function (Blueprint $table) {
            $table->dropColumn('percentage');
        });
    }
};
