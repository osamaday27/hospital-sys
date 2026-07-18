<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('asset_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code_prefix')->default('AS');
            $table->unsignedSmallInteger('qr_size_px')->default(300);
            $table->unsignedSmallInteger('qr_margin')->default(2);
            $table->boolean('enable_rfid')->default(false);
            $table->string('qr_disk')->default('public');
            $table->string('qr_path')->default('asset_qr');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('asset_settings'); }
};
