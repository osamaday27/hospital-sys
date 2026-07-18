<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierServicesTable extends Migration
{
    public function up()
    {
        Schema::create('suppliers_services', function (Blueprint $table) {
            $table->id();                                  // الكود الآلي
            $table->string('code')->unique();       // الكود اليدوي (مطلوب)
            $table->string('name_ar');                     // الاسم عربي (مطلوب)
            $table->string('name_en');                     // الاسم إنجليزي (مطلوب)
            $table->decimal('price', 15, 2);               // السعر (مطلوب)
            $table->text('description')->nullable();       // وصف الخدمة
            $table->text('notes')->nullable();             // ملاحظات
            $table->enum('status', ['active','inactive'])  // الحالة
                  ->default('active');
            $table->timestamps();
            $table->softDeletes();                         // إن احتجت الحذف الناعم
        });
    }

    public function down()
    {
        Schema::dropIfExists('suppliers_services');
    }
}
