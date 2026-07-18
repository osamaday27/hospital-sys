<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_d_lab_xray_requests', function (Blueprint $table) {
            $table->id();
            $table->string('id_patient')->nullable();
            $table->string('id_visit')->nullable();
            $table->string('id_reservation')->nullable();
            $table->string('type_visit')->nullable();
            $table->enum('request_type', ['lab', 'xray']); // نوع الطلب (معمل أو أشعة)
            $table->text('service_id')->nullable(); // تفاصيل الطلب
            $table->date('request_date')->nullable(); // تاريخ الطلب
            $table->string('status')->default('pending'); // حالة الطلب (معلق، مكتمل، إلخ)
            $table->string('eye')->nullable(); // حالة الطلب (معلق، مكتمل، إلخ)

            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // الطبيب المسجل
            $table->timestamps();
            $table->softDeletes(); // للحذف الناعم (إذا لزم الأمر)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_d_lab_xray_requests');
    }
};