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
        Schema::create('surgery_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surgery_id');
            $table->unsignedBigInteger('item_id'); // من store_products
            $table->decimal('quantity', 10, 2);
            $table->string('unit')->nullable(); // اختيار الوحدة (علبة، قطعة..)
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        
            $table->foreign('surgery_id')->references('id')->on('fin_services')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('surgery_items');
    }
};