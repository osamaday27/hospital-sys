<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('prescription_items', function (Blueprint $table) {
    $table->id();
    
    // 💡 تعديل هام: حقل رقمي عادي بدون ربط مباشر هنا لمنع انهيار الجدول أثناء البناء
    $table->unsignedBigInteger('medication_id'); 

    $table->string('medication_text')->nullable();
    $table->string('unit_text')->nullable();
    $table->string('duration_text')->nullable();
    $table->string('taken_text')->nullable();

    $table->unsignedBigInteger('unit_id')->nullable();
    $table->unsignedBigInteger('duration_id')->nullable();
    $table->unsignedBigInteger('taken_id')->nullable();
    
    $table->timestamps();
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
        Schema::dropIfExists('prescription_items');
    }
};
