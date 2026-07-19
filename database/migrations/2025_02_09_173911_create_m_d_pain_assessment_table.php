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
        Schema::create('m_d_pain_assessment', function (Blueprint $table) {
            $table->id();
            $table->string('id_patient')->nullable();
            $table->string('id_visit')->nullable();
            $table->string('id_reservation')->nullable();
            $table->string('type_visit')->nullable();
            $table->string('digital_assessment', 8, 2)->nullable(); // التقييم الرقمي
            $table->string('face_assessment', 8, 2)->nullable(); // تقييم الوجة
            $table->decimal('child_assessment', 8, 2)->nullable(); // تقييم الأطفال الأقل من 7 سنوات إلى شهرين
            $table->enum('pain_type', ['Dull', 'Burning', 'Cramping', 'Crushing', 'Heavy', 'Tingling', 'Pricking', 'Stabbing', 'Pulsating'])->nullable();// Pain type translation
            $table->text('notes')->nullable(); // ملاحظات 
            $table->string('status')->default('pending'); // حالة الطلب (معلق، مكتمل، إلخ)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // الطبيب المسجل
            $table->timestamps();
            $table->softDeletes(); // للحذف الناعم (إذا لزم الأمر)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_d_pain_assessment');
    }
};
