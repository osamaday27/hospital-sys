<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitSuppliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adm_b_visit_supplies', function (Blueprint $table) {
            // المفتاح الأساسي
            $table->increments('id');

            // مفاتيح الربط
            $table->unsignedInteger('reservation_id');
            $table->unsignedInteger('patient_id');
            $table->unsignedInteger('visit_id');
            $table->foreignId('product_id')
            ->constrained('products_data')
            ->cascadeOnDelete();
            
            $table->foreignId('store_id')
            ->constrained('store_main_date')
            ->cascadeOnDelete();
            
            $table->foreignId('unit_id')
            ->constrained('units')
            ->restrictOnDelete();
            
            // الكمية والسعر
            $table->decimal('quantity', 8, 2);
            $table->decimal('price', 10, 2);
            
            // المستخدم الذي أضاف السجل
        
            // المستخدم الذي أنشأ السجل (nullable في حال لم يتم تحديده)
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            // تعريف القيود (foreign keys)
            $table->foreign('reservation_id')
                  ->references('id')->on('adm_s_reservations')
                  ->onDelete('cascade');
            
            $table->foreign('patient_id')
                  ->references('id')->on('patient')
                  ->onDelete('cascade');
            
            $table->foreign('visit_id')
                  ->references('id')->on('patient_visits')
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
        Schema::dropIfExists('visit_supplies');
    }
}