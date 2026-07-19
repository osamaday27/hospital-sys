<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDamagedItemRequestItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('damaged_item_request_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->foreignId('product_id')->constrained('products_data');
            $table->foreignId('unit_id')->constrained('units');
            $table->decimal('quantity', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('request_id')->references('id')->on('damaged_item_requests')->onDelete('cascade');
           
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damaged_item_request_items');
    }
}
