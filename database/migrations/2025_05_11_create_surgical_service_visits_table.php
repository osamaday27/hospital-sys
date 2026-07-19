<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surgical_service_visits', function (Blueprint $table) {
            $table->id();

            $table->text('invoice_id')->nullable(); // أو غيّر اسم الجدول حسب الموجود عندك
            $table->text('visit_id')->nullable();
            $table->text('service_id')->nullable();
            $table->text('patient_id')->nullable();

            $table->integer('quantity')->default(1);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('contract_value', 12, 2)->default(0);
            $table->decimal('patient_value', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surgical_service_visits');
    }
};
