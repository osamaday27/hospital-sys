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
    Schema::create('product_units', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->constrained('products_data')->onDelete('cascade');
        $table->foreignId('unit_large_id')->nullable()->constrained('units')->onDelete('cascade');
        $table->foreignId('unit_small_id')->nullable()->constrained('units')->onDelete('cascade');


        $table->decimal('conversion_rate', 10, 2)->nullable();
        $table->decimal('cost_price', 10, 2)->nullable();
        $table->decimal('sale_price', 10, 2)->nullable();

        $table->enum('unit_type', ['purchase', 'sale', 'both', 'conversion']);

        $table->softDeletes();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productunits');
    }
};
