<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supplier_payment_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('installment_id')
                  ->constrained('supplier_payment_installments')
                  ->onDelete('cascade');
            $table->enum('type', ['due_soon','overdue'])->default('due_soon');
            $table->boolean('seen')->default(false);
            $table->timestamp('alert_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payment_alerts');
    }
};
