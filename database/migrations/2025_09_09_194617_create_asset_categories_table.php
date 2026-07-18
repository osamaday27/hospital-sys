<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('code')->unique();
            $table->unsignedInteger('useful_life_months')->nullable();
            $table->enum('default_dep_method', ['SL','DDB','UOP'])->nullable();
            $table->decimal('default_dep_rate', 8,4)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('asset_categories')->onDelete('set null');
            $table->index(['parent_id','is_active']);
        });
    }
    public function down(): void { Schema::dropIfExists('asset_categories'); }
};
