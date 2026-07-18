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
        Schema::create('employee_item_request_details_stroe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('employee_item_requests_stores')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products_data');
            $table->foreignId('unit_id')->constrained('units');
            $table->float('quantity');
            $table->text('status')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();      
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_item_request_details_stroe');
    }
};