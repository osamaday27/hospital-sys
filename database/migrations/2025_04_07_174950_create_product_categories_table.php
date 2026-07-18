<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            // نوع التصنيف: رئيسي (main) أو فرعي (sub)
            $table->enum('type', ['main', 'sub']);
            // الحالة: true أو false
            $table->boolean('status')->default(true);
            // قائمة الفروع بتنسيق JSON (يتم جلبها من جدول branches)
            $table->json('branch');
            // ترتيب التصنيف (يمكن أن يكون null)
            $table->integer('sort')->nullable();
            // التصنيف الأب (للتصنيفات الفرعية)، يمكن أن يكون null
            $table->unsignedBigInteger('category')->nullable();

            $table->timestamps();
            $table->softDeletes(); 

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('product_categories');
    }
}
