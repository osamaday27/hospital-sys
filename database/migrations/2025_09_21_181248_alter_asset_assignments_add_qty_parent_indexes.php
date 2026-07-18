<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('asset_assignments', function (Blueprint $table) {
            // كمية العملية: في سجلات assigned = كمية مُسندة، وفي returned = كمية مُرتجعة
            if (!Schema::hasColumn('asset_assignments', 'qty')) {
                $table->unsignedInteger('qty')->default(1)->after('status');
            }

            // ربط سجل الإرجاع بالأصل (إن لم تكن موجودة من قبل)
            if (!Schema::hasColumn('asset_assignments', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('asset_id');
                $table->index(['parent_id']);
            }

            // فهارس مفيدة للاستعلامات التجميعية
            $table->index(['asset_id','status']);
        });
    }

    public function down(): void {
        Schema::table('asset_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('asset_assignments', 'qty')) {
                $table->dropColumn('qty');
            }
            if (Schema::hasColumn('asset_assignments', 'parent_id')) {
                $table->dropIndex(['parent_id']);
                $table->dropColumn('parent_id');
            }
            $table->dropIndex(['asset_id','status']);
        });
    }
};
