<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_settings', function (Blueprint $table) {
            $table->id();
            // هل يوجد حد أقصى لأمر الشراء؟
            $table->boolean('has_max_limit')->default(false);
            // قيمة الحد الأقصى إذا وُجد
            $table->decimal('max_limit_value', 15, 2)->nullable();
            // استخدام آخر سعر بيع للمورد؟
            $table->boolean('use_last_supplier_price')->default(false);
            // استخدام سعر البيع؟
            $table->boolean('use_sale_price')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_settings');
    }
}
