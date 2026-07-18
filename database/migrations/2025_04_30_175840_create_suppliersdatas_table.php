<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers_data', function (Blueprint $table) {
            $table->id();

            // بيانات أساسية
            $table->string('code')->unique();
            $table->string('name_ar');
            $table->string('name_en');

            // المفاتيح الأجنبية للعلاقات الأساسية
            $table->unsignedBigInteger('suppliersgroup_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('suppliercompany_id');

            // نوع المورد: خدمي أو أصناف
            $table->enum('supplier_type', ['service', 'goods']);

            // أرقام رسمية
            $table->string('tax_card_number')->nullable();
            $table->string('commercial_registration_number')->nullable();

            // مجال الشركة
            $table->string('company_field')->nullable();

            // وسائل الاتصال (JSON)
            $table->json('contact_methods')->nullable();

            // الموقع الجغرافي باستخدام مفاتيح خارجية
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('government_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();

            // الحالة ومن أنشأ السجل
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();

            // تواريخ الإنشاء والتحديث والحذف
            $table->timestamps();
            $table->softDeletes();

            // تعريف المفاتيح الأجنبية
            $table->foreign('suppliersgroup_id')
                  ->references('id')->on('suppliers_groups')
                  ->onDelete('cascade');

            $table->foreign('branch_id')
                  ->references('id')->on('branches')
                  ->onDelete('restrict');

            $table->foreign('suppliercompany_id')
                  ->references('id')->on('supplier_companies')
                  ->onDelete('cascade');

            $table->foreign('country_id')
                  ->references('id')->on('countries')
                  ->onDelete('set null');

            $table->foreign('government_id')
                  ->references('id')->on('government')
                  ->onDelete('set null');

            $table->foreign('city_id')
                  ->references('id')->on('city')
                  ->onDelete('set null');

            $table->foreign('created_by')
                  ->references('id')->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suppliers_data');
    }
};
