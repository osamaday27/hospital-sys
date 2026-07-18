<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierdocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplierdocs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->string('file_type');
            $table->string('file_name');
            $table->string('file_path');
            $table->timestamps();
            $table->softDeletes();

            // مفتاح خارجي إلى جدول بيانات الموردين
            $table->foreign('supplier_id')
                  ->references('id')
                  ->on('suppliers_data')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplierdocs');
    }
}
