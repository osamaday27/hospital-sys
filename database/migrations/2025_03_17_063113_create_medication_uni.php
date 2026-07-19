<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('medication_units', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الوحدة (مثال: mg, ml, tablet)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medication_units');
    }
};
