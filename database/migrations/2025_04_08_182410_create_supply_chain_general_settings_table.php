<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplyChainGeneralSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supply_chain_general_settings', function (Blueprint $table) {
            $table->id();
            // سياسة سعر التكلفة
            $table->string('cost_policy')->nullable();
            // سياسة سعر البيع بالمخازن
            $table->string('sale_policy')->nullable();
            // طريقة تكويد الأصناف
            $table->string('item_coding_method')->nullable();
            // عدد أرقام كود الصنف بعد المجموعة
            $table->string('item_code_length')->nullable();
            // تكلفة الهدايا
            $table->string('gift_cost')->nullable();
            // ملصقات الباركود
            $table->string('barcode_labels')->nullable();
            // ربط المخازن بالمستخدمين
            $table->string('store_user_link')->nullable();
            // طريقة تكويد المسلسل العام (نوع الحركة)
            $table->string('general_serial_method')->nullable();
            // تكويد المسلسل العام (السنة)
            $table->string('financial_year_code')->nullable();
            // تحديد إن كانت طلبات الاستعاضة أوتوماتيك أم لا
            $table->boolean('auto_substitution')->default(false);
            // أيام الصرف (يُخزن كـ JSON)
            $table->json('expenditure_days')->nullable();
            // عدم تحديث سعر البيع في حالة انخفاض السعر
            $table->boolean('dont_update_sale_price')->default(false);

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
        Schema::dropIfExists('supply_chain_general_settings');
    }
}
