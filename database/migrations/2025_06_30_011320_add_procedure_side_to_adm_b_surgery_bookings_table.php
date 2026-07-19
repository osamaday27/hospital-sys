<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('adm_b_surgery_bookings', function (Blueprint $table) {
            $table->enum('procedure_side', ['right', 'left', 'both'])
                  ->nullable()
                  ->after('booking_date')
                  ->comment('مكان الإجراء الطبي (يمين - يسار - كلاهما)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('adm_b_surgery_bookings', function (Blueprint $table) {
            $table->dropColumn('procedure_side');
        });
    }
};
