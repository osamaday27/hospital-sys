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
            if (Schema::hasColumn('hr_contracts', 'social_insurance_number')) {
                $table->dropColumn('social_insurance_number');
            }
            if (Schema::hasColumn('hr_contracts', 'allowances')) {
                $table->dropColumn('allowances');
            }
            if (Schema::hasColumn('hr_contracts', 'medical_insurance')) {
                $table->dropColumn('medical_insurance');
            }
            if (Schema::hasColumn('hr_contracts', 'shohdaa')) {
                $table->dropColumn('shohdaa');
            }
            if (Schema::hasColumn('hr_contracts', 'social_insurance')) {
                $table->dropColumn('social_insurance');
            }
            if (Schema::hasColumn('hr_contracts', 'tax')) {
                $table->dropColumn('tax');
            }
            if (Schema::hasColumn('hr_contracts', 'net_salary')) {
                $table->dropColumn('net_salary');
            }

            if (!Schema::hasColumn('hr_contracts', 'salary_level')) {
                $table->enum('salary_level', ['by_houes', 'by_shift', 'monthly'])->after('status');
            }
            if (!Schema::hasColumn('hr_contracts', 'hour_amount')) {
                $table->float('hour_amount')->nullable()->after('salary_level');
            }
            if (!Schema::hasColumn('hr_contracts', 'shift_amount')) {
                $table->float('shift_amount')->nullable()->after('hour_amount');
            }
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
            $table->string('social_insurance_number')->nullable();
            $table->float('allowances')->default(0);
            $table->float('medical_insurance')->default(0);
            $table->float('shohdaa')->default(0);
            $table->float('social_insurance')->default(0);
            $table->float('tax')->default(0);
            $table->float('net_salary');
            if (Schema::hasColumn('hr_contracts', 'salary_level')) {
                $table->dropColumn('salary_level');
            }
            if (Schema::hasColumn('hr_contracts', 'hour_amount')) {
                $table->dropColumn('hour_amount');
            }
            if (Schema::hasColumn('hr_contracts', 'shift_amount')) {
                $table->dropColumn('shift_amount');
            }
        });
    }
};