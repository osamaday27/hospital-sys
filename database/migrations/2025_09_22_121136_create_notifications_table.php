<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
       Schema::create('notifications', function (Blueprint $table) {
    $table->bigIncrements('id');   // Auto increment
    $table->string('type')->nullable();
    $table->morphs('notifiable');
    $table->json('data');
    $table->timestamp('read_at')->nullable();
    $table->timestamps();
});

    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
