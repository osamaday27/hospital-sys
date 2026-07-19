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
        Schema::create('fin_invoice_cancellation_requests', function (Blueprint $table) {
            $table->id();
        
            // 👇 عدل هنا ليكون INT بدلاً من BIGINT
            $table->unsignedInteger('invoice_id');
            $table->foreign('invoice_id')->references('id')->on('fin_invoices')->onDelete('cascade');
        
            $table->text('cancellation_reason')->nullable();
            $table->text('cancellation_notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        
            $table->foreign('requested_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('fin_invoice_cancellation_requests');
    }
};
