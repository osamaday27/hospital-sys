<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('adm_b_surgery_bookings', function (Blueprint $table) {
            $table->text('custom_diagnosis')->nullable()->after('status')
                  ->comment('تشخيص يدوي يتم إدخاله في حالة عدم اختيار التشخيص من القائمة');
        });
    }

    public function down(): void
    {
        Schema::table('adm_b_surgery_bookings', function (Blueprint $table) {
            $table->dropColumn('custom_diagnosis');
        });
    }
};
