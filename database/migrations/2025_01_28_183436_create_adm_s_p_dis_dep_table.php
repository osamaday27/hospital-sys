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
        Schema::create('adm_s_p_dis_dep', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_department_id')->constrained('hr_department')->onDelete('cascade'); // معرف الإدارة
            $table->enum('status', ['active', 'inactive'])->default('active'); // الحالة
            $table->unsignedBigInteger('created_by')->nullable(); // الشخص الذي قام بالإنشاء
            $table->softDeletes(); // حذف ناعم
            $table->timestamps(); // الوقت
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adm_s_p_dis_dep');
    }
};