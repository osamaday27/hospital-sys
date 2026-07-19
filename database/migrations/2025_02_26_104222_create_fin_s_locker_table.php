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
        Schema::create('fin_s_locker', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar'); // اسم الخزنة
            $table->string('name_en'); // اسم الخزنة
            $table->enum('type', ['main', 'sub']); // نوع الخزنة (رئيسي/فرعي)
            $table->string('currency', 10)->default('EGP'); // العملة
            $table->string('has_daily_limit')->nullable(); // هل يوجد حد أقصى للصرف اليومي؟
            $table->boolean('stauts')->default(false); //
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
        Schema::dropIfExists('fin_s_locker');
    }
};
