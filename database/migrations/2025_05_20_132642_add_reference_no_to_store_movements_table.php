<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferenceNoToStoreMovementsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('store_movements', function (Blueprint $table) {
            $table->string('movement_number')->nullable()->after('movement_type')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_movements', function (Blueprint $table) {
            $table->dropColumn('movement_number');
        });
    }
}
