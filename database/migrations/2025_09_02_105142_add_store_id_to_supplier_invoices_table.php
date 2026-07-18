<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {
            // إضافة store_id وربطه بجدول المخازن (store_main_date)
            $table->unsignedBigInteger('store_id')->nullable()->after('branch_id');

            $table->foreign('store_id')
                  ->references('id')
                  ->on('store_main_date')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropColumn('store_id');
        });
    }
};