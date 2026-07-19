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
        Schema::create('adm_s_surgicaldatasettings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('fin_services')->onDelete('cascade');
            $table->string('name_ar'); // اسم العملية الجراحية بالعربية
            $table->string('name_en'); // اسم العملية الجراحية بالإنجليزية
            $table->text('description_ar')->nullable(); // وصف العملية بالعربية
            $table->text('description_en')->nullable(); // وصف العملية بالإنجليزية

            $table->enum('category', ['minor', 'major', 'advanced']); // تصنيف العملية
            $table->enum('technique', ['open', 'laparoscopic', 'robotic', 'other']); // التقنية المستخدمة
            $table->time('expected_duration')->nullable(); // المدة المتوقعة
            $table->enum('complexity', ['simple', 'moderate', 'complex']); // درجة التعقيد
            $table->text('possible_complications_ar')->nullable(); // المضاعفات المحتملة بالعربية
            $table->text('possible_complications_en')->nullable(); // المضاعفات المحتملة بالإنجليزية

            // 🔹 تحضير المريض
            $table->text('preparation_instructions_ar')->nullable(); // تعليمات الصيام بالعربية
            $table->text('preparation_instructions_en')->nullable(); // تعليمات الصيام بالإنجليزية
            $table->json('pre_op_tests')->nullable(); // الفحوصات المطلوبة بصيغة JSON
            $table->json('pre_op_medications')->nullable(); // الأدوية الوقائية بصيغة JSON
            $table->boolean('blood_compatibility_test')->default(false); // تحليل توافق الدم

            // 🔹 تفاصيل العملية
            $table->enum('surgical_method', ['open', 'laparoscopic', 'robotic'])->default('open'); // نوع الجراحة
            $table->json('surgical_tools')->nullable(); // الأدوات الجراحية بصيغة JSON

            // 🔹 التخدير والجهاز التنفسي
            $table->enum('anesthesia_type', ['general', 'local', 'regional', 'other'])->default('general'); // نوع التخدير
            $table->json('anesthesia_medications')->nullable(); // الأدوية المخدرة بصيغة JSON
            $table->time('anesthesia_duration')->nullable(); // مدة التخدير
            $table->enum('ventilation_method', ['mask', 'laryngeal_tube', 'spontaneous'])->nullable(); // طريقة التهوية

         

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
        Schema::dropIfExists('adm_s_surgicaldatasettings');
    }
};
