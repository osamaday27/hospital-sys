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
public function up(): void
{
    Schema::table('asset_audit_lines', function (Blueprint $table) {
        $table->json('draft_asset_data')->nullable()->after('result');
    });
}

public function down(): void
{
    Schema::table('asset_audit_lines', function (Blueprint $table) {
        $table->dropColumn('draft_asset_data');
    });
}

};
