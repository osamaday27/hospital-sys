<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierCompaniesTable extends Migration
{
    public function up()
{
    Schema::create('supplier_companies', function (Blueprint $table) {
        $table->id();
        $table->string('manual_code')->unique();        // الكود اليدوي
        $table->string('name_ar');                      // الاسم عربي
        $table->string('name_en');                      // الاسم إنجليزي
        $table->string('activity')->nullable();         // النشاط
        $table->string('commercial_registration_no')->nullable();   // رقم السجل التجاري
        $table->string('tax_card_no')->nullable();      // رقم البطاقة الضريبية
        $table->string('domain')->nullable();           // مجال الشركة
        $table->string('phone')->nullable();            // رقم الهاتف
        $table->string('email')->nullable();            // الإيميل
        $table->string('fax')->nullable();              // الفاكس

        // العلاقات الجغرافية
        $table->unsignedBigInteger('country_id')->nullable();
        $table->unsignedBigInteger('government_id')->nullable();
        $table->unsignedBigInteger('city_id')->nullable();

        $table->text('notes')->nullable();              // ملاحظات
        $table->enum('status', ['active', 'inactive'])->default('active');
        $table->timestamps();
        $table->softDeletes();

        // ربط المفاتيح الخارجية
        $table->foreign('country_id')
        ->references('id')->on('countries')
        ->onDelete('set null');

  $table->foreign('government_id')
        ->references('id')->on('government')
        ->onDelete('set null');

  $table->foreign('city_id')
        ->references('id')->on('city')
        ->onDelete('set null');
    });
}

    public function down()
    {
        Schema::dropIfExists('supplier_companies');
    }
}
