<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fin_b_gl_entries', function (Blueprint $table) {
            $table->id();
            $table->date('date');                               // تاريخ القيد
            $table->string('reference_type')->nullable();        // نوع المرجع (فاتورة/إيصال...)
            $table->unsignedBigInteger('reference_id')->nullable(); // رقم المرجع
            $table->text('description')->nullable();             // الوصف
            $table->unsignedBigInteger('created_by')->nullable();// المستخدم
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_b_gl_entries');
    }
};