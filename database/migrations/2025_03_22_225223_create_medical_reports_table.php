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
        Schema::create('m_d_medical_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_patient');
            $table->unsignedBigInteger('id_visit');
            $table->unsignedBigInteger('id_reservation');
            $table->string('type_visit');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null'); // الطبيب الذي وصف الدواء
            $table->text('content')->nullable();
            $table->date('report_date')->default(now()); // تاريخ التقرير
            $table->enum('status', ['pending', 'completed', 'canceled'])->default('pending'); // حالة التقرير
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
        Schema::dropIfExists('m_d_medical_reports');
    }
};
