<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('asset_items', function (Blueprint $table) {
            // كمية الأصل (عدد القطع من نفس الأصل)
            $table->unsignedInteger('qty')->default(1)->after('cost');
        });
    }

    public function down(): void {
        Schema::table('asset_items', function (Blueprint $table) {
            $table->dropColumn('qty');
        });
    }
};
