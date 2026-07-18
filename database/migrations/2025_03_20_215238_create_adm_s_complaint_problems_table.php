<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmSComplaintProblemsTable extends Migration
{
    public function up()
    {
        Schema::create('adm_s_complaint_problems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('name_en');
            $table->string('name_ar');
            $table->boolean('status')->default(1);
            $table->timestamps();
            $table->softDeletes();

            // علاقة بفئات الشكوى
            $table->foreign('category_id')
                  ->references('id')
                  ->on('adm_s_complaint_categories')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('adm_s_complaint_problems');
    }
}
