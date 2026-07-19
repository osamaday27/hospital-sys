<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDentalIssuesTable extends Migration
{
    public function up()
    {
        Schema::create('m_d_dental_issues', function (Blueprint $table) {
            $table->id();
            // روابط وتعريفات عامة
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('id_reservation');
            $table->unsignedBigInteger('id_visit');
            $table->string('type_visit')->nullable();

            // رقم السن (يدعم أرقام أو حروف للأسنان اللبنية)
            $table->string('tooth');

            // البيانات الطبية
            $table->text('complaint')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('treatment_plan')->nullable();
            $table->text('history')->nullable();

            // الإجراءات الإضافية
            $table->boolean('missing')->default(false); // السن غير موجودة
            $table->boolean('filled')->default(false);  // محشوة
            $table->boolean('crowned')->default(false); // تاج
            $table->boolean('abscess')->default(false); // خراج
            $table->boolean('implant')->default(false); // زراعة
            $table->boolean('extracted')->default(false); // مستخرج

            // معلومات تتبع
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_d_dental_issues');
    }
}
