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
        Schema::create('supplier_general_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('notify_return_from_warehouse')->default(false);
            $table->boolean('notify_discount_on_service_invoice')->default(false);
            $table->boolean('notify_discount_on_supply_invoice')->default(false);
            $table->decimal('rounding_allowed_value', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_general_settings');
    }
};
