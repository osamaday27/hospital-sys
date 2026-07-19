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
        Schema::create('m_s_medical_report_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('content');
            $table->boolean('is_global')->default(false); // قالب عام للجميع أو لا
            $table->unsignedBigInteger('specialty_id')->nullable(); // التخصص
            $table->foreign('specialty_id')->references('id')->on('specialties')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // الطبيب الذي وصف الدواء
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
        Schema::dropIfExists('m_s_medical_report_templates');
    }
};
