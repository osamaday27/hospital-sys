<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('asset_items')) {
            return;
        }

        if (!Schema::hasColumn('asset_items', 'qty')) {

            Schema::table('asset_items', function (Blueprint $table) {

                if (Schema::hasColumn('asset_items', 'cost')) {
                    $table->unsignedInteger('qty')
                        ->default(1)
                        ->after('cost');
                } else {
                    // إذا لم يوجد cost أضف العمود في نهاية الجدول
                    $table->unsignedInteger('qty')
                        ->default(1);
                }

            });

        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('asset_items')) {
            return;
        }

        if (Schema::hasColumn('asset_items', 'qty')) {

            Schema::table('asset_items', function (Blueprint $table) {
                $table->dropColumn('qty');
            });

        }
    }
};