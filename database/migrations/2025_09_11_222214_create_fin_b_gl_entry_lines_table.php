<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fin_b_gl_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entry_id');          // يربط بالسطر الرئيسي
            $table->unsignedBigInteger('account_id');        // الحساب (من شجرة الحسابات)
            $table->decimal('debit', 15, 2)->default(0);     // المبلغ المدين
            $table->decimal('credit', 15, 2)->default(0);    // المبلغ الدائن
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('entry_id')->references('id')->on('fin_b_gl_entries')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('fin_s_gl_accounts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_b_gl_entry_lines');
    }
};