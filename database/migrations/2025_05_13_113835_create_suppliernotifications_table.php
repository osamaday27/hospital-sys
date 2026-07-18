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
        Schema::create('supplier_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('general_setting_id')
                  ->constrained('supplier_general_settings')
                  ->onDelete('cascade');
            $table->string('label');
            $table->decimal('value', 15, 2);
            $table->enum('value_type', ['fixed', 'percentage']);
            $table->enum('action_type', ['discount', 'addition']);
            $table->enum('category', [
                'tax',
                'additional',
                'collection_tax',
                'invoice_expense',
                'freelance_services',
                'stamp_duty',
                'income_tax'
            ]);
            $table->timestamps();
            $table->softDeletes();  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_notifications');
    }
};
