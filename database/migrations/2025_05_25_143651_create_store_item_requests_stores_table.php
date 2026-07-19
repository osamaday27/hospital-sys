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
        Schema::create('store_item_requests_stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_to_id')->constrained('hr_sections');
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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_item_requests_stores');
    }
};
