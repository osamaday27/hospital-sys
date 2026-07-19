<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('fin_claims', function (Blueprint $table) {
            // تاريخ الفترة من/إلى
            $table->date('period_from')->nullable()->after('status');
            $table->date('period_to')->nullable()->after('period_from');

            // لو حابب تسيب الأعمدة القديمة شغّالة، سيبها زي ما هي.
            // لو عايز تخليها Nullable تأكيدًا:
            // ⚠️ يحتاج doctrine/dbal لو هتغيّر خصائص أعمدة موجودة:
            // composer require doctrine/dbal
            // $table->unsignedBigInteger('invoice_id')->nullable()->change();
            // $table->unsignedBigInteger('patient_id')->nullable()->change();
        });
    }

    public function down(): void {
        Schema::table('fin_claims', function (Blueprint $table) {
            $table->dropColumn(['period_from', 'period_to']);
        });
    }
};
