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
        Schema::create('adm_s_admission_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar'); // الاسم بالعربي
            $table->string('name_en'); // الاسم بالإنجليزي
            $table->enum('status', ['active', 'inactive'])->default('active'); // الحالة
            $table->enum('type', ['entry', 'exit','entry_sources',''])->default('entry'); // الحالة
            $table->unsignedBigInteger('created_by')->nullable(); // الشخص الذي قام بالإنشاء
            $table->softDeletes(); // حذف ناعم
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
        Schema::dropIfExists('adm_s_admission_reasons');
    }
};
