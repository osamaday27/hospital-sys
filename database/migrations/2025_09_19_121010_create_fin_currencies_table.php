<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fin_currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // EGP, USD, EUR
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->decimal('exchange_rate', 15, 6)->default(1); // مقابل العملة المحلية
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_currencies');
    }
};
