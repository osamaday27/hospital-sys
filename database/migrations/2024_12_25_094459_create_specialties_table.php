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
        Schema::create('specialties', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar'); // اسم التخصص بالعربية
            $table->string('name_en'); // اسم التخصص بالإنجليزية
            $table->string('logo')->nullable(); // شعار التخصص
            $table->enum('status', ['active', 'inactive'])->default('active'); // الحالة
            $table->unsignedBigInteger('user_id')->nullable(); // معرف المستخدم الذي أنشأ القائمة
            $table->timestamps();
            $table->softDeletes();  // لحذف السجلات بشكل ناعم (اختياري)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('specialties');
    }
};
