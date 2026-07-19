<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hr_contracts', function (Blueprint $table) {
            // Drop the existing enum column
            $table->dropColumn('salary_structure_type');
        });

        Schema::table('hr_contracts', function (Blueprint $table) {
            // Add the new enum column with updated values
            $table->enum('salary_structure_type', ['operations', 'marketing', 'warehouse', 'physicians', 'admins'])->after('job_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hr_contracts', function (Blueprint $table) {
            // Drop the new enum column
            $table->dropColumn('salary_structure_type');
        });

        Schema::table('hr_contracts', function (Blueprint $table) {
            // Restore the original enum column
            $table->enum('salary_structure_type', ['physician', 'nursing', 'admin'])->after('job_id');
        });
    }
}; 
