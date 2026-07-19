<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('costing_gl_mappings', function (Blueprint $table) {
            $table->id();

            // أبعاد الترحيل
            $table->enum('line_type', ['direct','overhead'])->index();      // مباشر/غير مباشر
            $table->enum('item_type', ['supply','drug','resource','misc','overhead'])->index();
            $table->unsignedBigInteger('cost_center_id')->nullable()->index(); // اختياري للتخصيص حسب مركز

            // حسابات القيود
            $table->string('debit_account', 50);   // مثال: 510100
            $table->string('credit_account', 50);  // مثال: 130100
            $table->string('description')->nullable();

            // حالة وتواريخ
            $table->boolean('active')->default(true)->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['line_type','item_type','cost_center_id','debit_account','credit_account'], 'uniq_map');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('costing_gl_mappings');
    }
};
