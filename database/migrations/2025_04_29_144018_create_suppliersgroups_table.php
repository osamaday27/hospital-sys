<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('suppliers_groups', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique();
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('description')->nullable();

            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');


            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('suppliers_groups');
    }
}
