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
    Schema::create('logs', function (Blueprint $table) {
        $table->id();
        $table->string('action_type', 50); // create, update, delete
        $table->string('table_name', 100)->nullable();
        $table->bigInteger('record_id')->nullable();
        $table->string('field_name', 100)->nullable();
        $table->text('old_value')->nullable();
        $table->text('new_value')->nullable();
        $table->bigInteger('performed_by');
        $table->timestamp('performed_at')->useCurrent();
        $table->string('method', 10)->nullable();
        $table->text('full_url')->nullable();
        $table->string('ip_address', 50)->nullable();
        $table->string('mac_address', 50)->nullable();
        $table->text('user_agent')->nullable();
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
        Schema::dropIfExists('logs');
    }
};