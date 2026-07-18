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
        // First, modify the enum to include both old and new values
        DB::statement("ALTER TABLE hr_contracts MODIFY COLUMN salary_level ENUM('by_houes', 'by_shift', 'monthly', 'hours', 'shifts')");
        
        // Update existing contract salary levels to new format
        DB::table('hr_contracts')
            ->where('salary_level', 'by_houes')
            ->update(['salary_level' => 'hours']);
            
        DB::table('hr_contracts')
            ->where('salary_level', 'by_shift')
            ->update(['salary_level' => 'shifts']);
            
        // Finally, remove the old enum values
        DB::statement("ALTER TABLE hr_contracts MODIFY COLUMN salary_level ENUM('hours', 'shifts', 'monthly')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // First, modify the enum to include both old and new values
        DB::statement("ALTER TABLE hr_contracts MODIFY COLUMN salary_level ENUM('by_houes', 'by_shift', 'monthly', 'hours', 'shifts')");
        
        // Revert to old salary level format
        DB::table('hr_contracts')
            ->where('salary_level', 'hours')
            ->update(['salary_level' => 'by_houes']);
            
        DB::table('hr_contracts')
            ->where('salary_level', 'shifts')
            ->update(['salary_level' => 'by_shift']);
            
        // Finally, restore original enum values
        DB::statement("ALTER TABLE hr_contracts MODIFY COLUMN salary_level ENUM('by_houes', 'by_shift', 'monthly')");
    }
};
