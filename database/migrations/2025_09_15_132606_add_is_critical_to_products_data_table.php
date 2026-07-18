<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products_data', function (Blueprint $table) {
            // عمود صنف حرج: 0 = غير حرج، 1 = حرج
            $table->boolean('is_critical')
                  ->default(false)
                  ->after('product_type'); 
        });
    }

    public function down(): void
    {
        Schema::table('products_data', function (Blueprint $table) {
            $table->dropColumn('is_critical');
        });
    }
};
