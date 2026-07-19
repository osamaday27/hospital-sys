<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDamagedItemRequestsTable extends Migration
{
    public function up(): void
    {
        Schema::create('damaged_item_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('store_id')->constrained('store_main_date');
            $table->date('request_date');
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['new', 'approved', 'rejected'])->default('new');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damaged_item_requests');
    }
}
