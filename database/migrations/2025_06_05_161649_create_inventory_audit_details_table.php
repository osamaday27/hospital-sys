<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_audit_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_audit_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('units_id');
            $table->decimal('system_quantity', 15, 4)->default(0);
            $table->decimal('actual_quantity', 15, 4)->default(0);
            $table->decimal('difference_quantity', 15, 4)->default(0);
            $table->enum('difference_type', ['surplus', 'shortage'])->nullable();
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        
            $table->foreign('inventory_audit_id')->references('id')->on('inventory_audits');
            $table->foreign('product_id')->references('id')->on('products_data');
            $table->foreign('units_id')->references('id')->on('units');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_audit_details');
    }
};
