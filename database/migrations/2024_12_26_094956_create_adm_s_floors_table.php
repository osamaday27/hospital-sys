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
        Schema::create('adm_s_floors', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar'); // اسم الدور بالعربية
            $table->string('name_en'); // اسم الدور بالإنجليزية
            $table->enum('type', ['administrative', 'medical','all']); // نوع الدور: إداري أو طبي
            $table->integer('rooms_count'); // عدد الغرف
            $table->foreignId('building_id')->constrained('adm_s_buildings')->onDelete('cascade'); // ارتباط بالمبنى
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
        Schema::dropIfExists('adm_s_floors');
    }
};