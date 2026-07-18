<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('item_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('request_number')->unique();       // رقم الطلب
            $table->date('request_date');                     // تاريخ الطلب
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // المستخدم الذي أنشأ الطلب
            $table->foreignId('store_id')
            ->constrained('store_main_date')
            ->cascadeOnDelete();            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('item_requests');
    }
}
