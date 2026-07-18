<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /* Helpers: فحص/حذف الـ FK بأمان قبل Schema::table */
    protected function dropForeignIfExists(string $table, string $constraint): void
    {
        try {
            $db = DB::getDatabaseName();
            $exists = DB::selectOne("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = ?
                  AND TABLE_NAME = ?
                  AND CONSTRAINT_NAME = ?
                  AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                LIMIT 1
            ", [$db, $table, $constraint]);

            if ($exists) {
                DB::statement("ALTER TABLE `$table` DROP FOREIGN KEY `$constraint`");
            }
        } catch (\Throwable $e) {
            // تجاهُل بأمان
        }
    }

    public function up(): void
    {
        $storesTable = (new \App\Models\supply_chain\storemaindata)->getTable();         // stores
        $productsTable     = (new \App\Models\Supply_Chain\ProductData)->getTable();     // products_data
        $productUnitsTable = (new \App\Models\supply_chain\productunit)->getTable();     // product_units
        $usersTable        = (new \App\Models\User)->getTable();                         // users

        /* -------- inventory_audits: إسقاط الـFKs بأمان أولاً -------- */
        $this->dropForeignIfExists('inventory_audits', 'inventory_audits_store_id_foreign');
        $this->dropForeignIfExists('inventory_audits', 'inventory_audits_user_id_foreign');

        Schema::table('inventory_audits', function (Blueprint $table) use ($storesTable, $usersTable) {
            if (!Schema::hasColumn('inventory_audits', 'code')) {
                $table->string('code', 40)->after('id')->unique();
            }
            if (!Schema::hasColumn('inventory_audits', 'start_date')) {
                $table->date('start_date')->nullable()->after('audit_date');
            }
            if (!Schema::hasColumn('inventory_audits', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('inventory_audits', 'committee_user_ids')) {
                $table->json('committee_user_ids')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('inventory_audits', 'attachments')) {
                $table->json('attachments')->nullable()->after('committee_user_ids');
            }

            // فهرس للتاريخ (يتجاهل لو موجود)
            try { $table->index('audit_date', 'idx_inventory_audits_audit_date'); } catch (\Throwable $e) {}

            // إعادة إضافة الـFKs
            $table->foreign('store_id')->references('id')->on($storesTable)->cascadeOnUpdate();
            $table->foreign('user_id')->references('id')->on($usersTable)->cascadeOnUpdate();
        });

        // تحويل status من ENUM إلى VARCHAR (بدون DBAL)
        try {
            DB::statement("ALTER TABLE `inventory_audits` MODIFY `status` VARCHAR(50) NULL DEFAULT NULL");
        } catch (\Throwable $e) {
            // بديل احتياطي
            if (!Schema::hasColumn('inventory_audits', 'status2')) {
                Schema::table('inventory_audits', fn(Blueprint $t) => $t->string('status2', 50)->nullable()->after('user_id'));
                try { DB::statement("UPDATE `inventory_audits` SET `status2` = `status`"); } catch (\Throwable $e2) {}
            }
        }

        /* -------- inventory_audit_details: إسقاط الـFKs بأمان أولاً -------- */
        $this->dropForeignIfExists('inventory_audit_details', 'inventory_audit_details_units_id_foreign');
        $this->dropForeignIfExists('inventory_audit_details', 'inventory_audit_details_inventory_audit_id_foreign');
        $this->dropForeignIfExists('inventory_audit_details', 'inventory_audit_details_product_id_foreign');
        $this->dropForeignIfExists('inventory_audit_details', 'inventory_audit_details_product_unit_id_foreign');

        Schema::table('inventory_audit_details', function (Blueprint $table) {
            // نضيف product_unit_id بدل renameColumn لتفادي DBAL
            if (!Schema::hasColumn('inventory_audit_details', 'product_unit_id')) {
                $table->unsignedBigInteger('product_unit_id')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('inventory_audit_details', 'batch_no')) {
                $table->string('batch_no', 100)->nullable()->after('product_unit_id');
            }
            if (!Schema::hasColumn('inventory_audit_details', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('batch_no');
            }
            if (!Schema::hasColumn('inventory_audit_details', 'status')) {
                $table->string('status', 20)->nullable()->after('difference_quantity');
            }

            // فهرس مركّب للاستعلامات
            try {
                $table->index(
                    ['inventory_audit_id','product_id','product_unit_id','batch_no','expiry_date'],
                    'idx_inv_audit_details_lookup'
                );
            } catch (\Throwable $e) {}
        });

        // ضبط الديفولت للكميات (SQL مباشر بدل change())
        try { DB::statement("ALTER TABLE `inventory_audit_details` MODIFY `system_quantity` DECIMAL(15,4) NOT NULL DEFAULT 0"); } catch (\Throwable $e) {}
        try { DB::statement("ALTER TABLE `inventory_audit_details` MODIFY `actual_quantity` DECIMAL(15,4) NOT NULL DEFAULT 0"); } catch (\Throwable $e) {}
        try { DB::statement("ALTER TABLE `inventory_audit_details` MODIFY `difference_quantity` DECIMAL(15,4) NOT NULL DEFAULT 0"); } catch (\Throwable $e) {}

        // إعادة إضافة الـFKs بعد التعديلات
        Schema::table('inventory_audit_details', function (Blueprint $table) use ($productsTable, $productUnitsTable) {
            $table->foreign('inventory_audit_id')->references('id')->on('inventory_audits')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('product_id')->references('id')->on($productsTable)->cascadeOnUpdate();
            $table->foreign('product_unit_id')->references('id')->on($productUnitsTable)->cascadeOnUpdate();
        });

        // ملء status تلقائيًا وفق الفرق
        try { DB::statement("UPDATE `inventory_audit_details` SET `status`='match' WHERE (`status` IS NULL OR `status`='') AND `difference_quantity`=0"); } catch (\Throwable $e) {}
        try { DB::statement("UPDATE `inventory_audit_details` SET `status`='surplus' WHERE (`status` IS NULL OR `status`='') AND `difference_quantity`>0"); } catch (\Throwable $e) {}
        try { DB::statement("UPDATE `inventory_audit_details` SET `status`='shortage' WHERE (`status` IS NULL OR `status`='') AND `difference_quantity`<0"); } catch (\Throwable $e) {}

        // محاولة تحويل status إلى ENUM ثلاثي (لو مدعوم)
        try {
            DB::statement("ALTER TABLE `inventory_audit_details` MODIFY `status` ENUM('surplus','shortage','match') NULL");
        } catch (\Throwable $e) {
            try { Schema::table('inventory_audit_details', fn(Blueprint $t) => $t->index('status', 'idx_inv_audit_details_status')); } catch (\Throwable $e2) {}
        }
    }

    public function down(): void
    {
        // إزالة الفهارس التي أضفناها فقط (تراجع آمن)
        try { Schema::table('inventory_audits', fn(Blueprint $t) => $t->dropIndex('idx_inventory_audits_audit_date')); } catch (\Throwable $e) {}
        try { Schema::table('inventory_audit_details', fn(Blueprint $t) => $t->dropIndex('idx_inv_audit_details_lookup')); } catch (\Throwable $e) {}
        try { Schema::table('inventory_audit_details', fn(Blueprint $t) => $t->dropIndex('idx_inv_audit_details_status')); } catch (\Throwable $e) {}
    }
};
