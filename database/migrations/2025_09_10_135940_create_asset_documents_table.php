<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('asset_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id');
            $table->enum('doc_type',['invoice','warranty','other'])->default('other');
            $table->string('doc_no',190)->nullable();
            $table->date('doc_date')->nullable();

            $table->unsignedBigInteger('supplier_id')->nullable(); // suppliers_data.id

            $table->decimal('cost',12,2)->nullable();
            $table->string('file_path',255)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('asset_items')->cascadeOnDelete();
            $table->foreign('supplier_id')->references('id')->on('suppliers_data')->nullOnDelete();

            $table->index(['asset_id','doc_type']);
            $table->index('supplier_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('asset_documents');
    }
};
