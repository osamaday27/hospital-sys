<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('medication_durations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // مدة الاستخدام (مثال: أسبوع، شهر، 10 أيام)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medication_durations');
    }
};
