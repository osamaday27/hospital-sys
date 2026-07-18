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
            // وقت وتاريخ تأكيد الحجز
            $table->timestamp('confirmed_at')
                  ->nullable()
                  ->after('reservation_note')
                  ->comment('وقت وتاريخ تأكيد الحجز');

            // وقت وتاريخ تأكيد الوصول
            $table->timestamp('arrival_confirmed_at')
                  ->nullable()
                  ->after('confirmed_at')
                  ->comment('وقت وتاريخ تأكيد وصول المريض');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('adm_s_reservations', function (Blueprint $table) {
            $table->dropColumn(['confirmed_at', 'arrival_confirmed_at']);
        });
    }
};