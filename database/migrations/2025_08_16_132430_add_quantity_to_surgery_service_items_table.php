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
        Schema::table('surgery_service_items', function (Blueprint $table) {
            // إضافة العمود quantity
            $table->decimal('quantity', 10, 2)->default(1)->after('service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surgery_service_items', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};