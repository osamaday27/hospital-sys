 
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
        Schema::create('medication_takens', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar'); // الاسم بالعربي (مثل: فموي، حقن، إلخ)
            $table->string('name_en'); // الاسم بالإنجليزي
            $table->tinyInteger('status')->default(1); // الحالة
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
        Schema::dropIfExists('medication_takens');
    }
};
