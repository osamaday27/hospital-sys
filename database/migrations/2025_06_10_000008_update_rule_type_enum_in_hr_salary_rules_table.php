<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update the enum values for rule_type column
        DB::statement("ALTER TABLE hr_salary_rules MODIFY COLUMN rule_type ENUM('absence', 'lateness', 'penalties', 'loans', 'incentive', 'overtime', 'free', 'manual', 'basic_salary', 'allowances', 'tax', 'insurance', 'gross_salary', 'net_salary') NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE hr_salary_rules MODIFY COLUMN rule_type ENUM('absence', 'lateness', 'penalties', 'loans', 'incentive', 'overtime', 'free', 'manual') NOT NULL");
    }
}; 