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
        Schema::create('fin_discount_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('invoice_id');
            $table->foreign('invoice_id')->references('id')->on('fin_invoices')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount', 10, 2);
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->unsignedBigInteger('action_by')->nullable();
            $table->timestamp('action_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fin_discount_requests');
    }
};
