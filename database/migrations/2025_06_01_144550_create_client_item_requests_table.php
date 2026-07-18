<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('client_item_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('client_id'); // <-- مهم جداً أن يكون unsignedInteger
            $table->foreignId('store_id')->constrained('store_main_date');
            $table->date('request_date')->default(now());
            $table->text('reason')->nullable();
            $table->enum('status', ['new', 'approved', 'rejected', 'issued','processed'])->default('new');
            $table->string('request_number')->unique();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            
        
            $table->foreign('client_id')->references('id')->on('patient')->onDelete('cascade');
        });
        

        Schema::create('client_item_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('client_item_requests')->onDelete('cascade');
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

    public function down(): void
    {
        Schema::dropIfExists('client_item_requests');
        Schema::dropIfExists('client_item_request_items');

    }
};