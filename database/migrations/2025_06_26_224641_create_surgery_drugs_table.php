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
        Schema::create('surgery_drugs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surgery_id');
            $table->unsignedBigInteger('drug_id'); // من جدول pharmacy_products
            $table->decimal('dose', 8, 2)->nullable();
            $table->string('unit')->nullable(); // mg, ml
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
        Schema::dropIfExists('surgery_drugs');
    }
};
