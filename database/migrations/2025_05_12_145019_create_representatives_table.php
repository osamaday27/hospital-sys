<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('representatives', function (Blueprint $table) {
            $table->id();

            // المفتاح الخارجي للمورد
            $table->unsignedBigInteger('supplier_id');

            // الأسماء باللغتين
            $table->string('name_ar');
            $table->string('name_en');

            // المسمى الوظيفي
            $table->string('job_title')->nullable();

            // التواصل
            $table->string('email')->unique();
            $table->string('phone')->nullable();

            // الحالة
            $table->enum('status', ['active', 'inactive'])->default('active');

            // تواريخ الإنشاء والتحديث والحذف
            $table->timestamps();
            $table->softDeletes();

            // تعريف FK
            $table->foreign('supplier_id')
                  ->references('id')
                  ->on('suppliers_data')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('representatives');
    }
};
