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
        Schema::create('gs_name_dictionaries', function (Blueprint $table) {
            $table->id();
            $table->text('name_ar'); // اسم الشخص أو الكلمة
            $table->text('name_en'); // اسم الشخص أو الكلمة
            $table->enum('gender', ['male', 'female', 'undefined'])->default('undefined'); // الحالة
            $table->enum('status', ['active', 'inactive'])->default('active'); // الحالة
            $table->unsignedBigInteger('user_id')->nullable(); // معرف المستخدم الذي أنشأ القائمة
            $table->softDeletes();
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
        Schema::dropIfExists('gs_name_dictionaries');
    }
};
