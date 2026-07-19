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
        Schema::create('adm_s_doctor_apologies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('clinic_id')->nullable(); // قد تكون لعيادة واحدة أو جميع العيادات
            $table->date('date_from');
            $table->date('date_to');
            $table->json('shifts');  // عمود لتخزين مجموعة من المناوبات
            $table->enum('status', ['new', 'confirmed', 'cancelled'])->default('new');
            $table->string('reason');
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('adm_s_doctor_apologies');
    }
};
