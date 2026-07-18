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
        Schema::create('m_d_pain_assessment_doc', function (Blueprint $table) {
            $table->id();
            $table->string('id_patient')->nullable();
            $table->string('id_visit')->nullable();
            $table->string('id_reservation')->nullable();
            $table->string('id_mdpainassessment')->nullable();
            $table->string('assessment')->nullable();
            $table->integer('degree')->nullable();
            $table->string('pain_location')->nullable();
            $table->string('continuous_intermittent')->nullable();
            $table->string('pain_type')->nullable();
            $table->string('increases_during')->nullable();
            $table->string('decreases_during')->nullable();
            $table->string('treatment')->nullable();
            $table->string('reassessment')->nullable();
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
        Schema::dropIfExists('m_d_pain_assessment_doc');
    }
};