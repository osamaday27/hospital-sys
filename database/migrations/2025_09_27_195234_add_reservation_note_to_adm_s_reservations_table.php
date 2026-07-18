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
        Schema::table('adm_s_reservations', function (Blueprint $table) {
            $table->text('reservation_note')
                  ->nullable()
                  ->after('case_type')
                  ->comment('ملاحظات الحجز');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('adm_s_reservations', function (Blueprint $table) {
            $table->dropColumn('reservation_note');
        });
    }
};