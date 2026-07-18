<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductLookupController;
use App\Http\Controllers\supply_chain\ApprovalController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\Supply_Chain\return_itemscontroller;
use App\Http\Controllers\SupplyChain\ProductsLookupController;
use App\Http\Controllers\supply_chain\adjust_balancecontroller;
use App\Http\Controllers\supply_chain\inventoryauditcontroller;
use App\Http\Controllers\supply_chain\edit_item_expirycontroller;
use App\Http\Controllers\supply_chain\return_to_suppliercontroller;
use App\Http\Controllers\Supply_Chain\UnitConversionTestController;

Auth::routes(['verify' => true]);

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'auth', 'verified'],
    ],
    function () {
        //call_center
        Route::group(['namespace' => 'supply_chain'], function () {
            Route::resource('supply_chain_settings', 'supply_chain_settingscontroller');
            Route::resource('supply_chain_program', 'supply_chain_programcontroller');
            Route::resource('supply_chain_reports', 'supply_chain_reportscontroller');

            //Product Categories
            Route::resource('product_categories', 'product_categoriescontroller');

            //System Settings
            Route::resource('store_settings', 'store_settingscontroller');

            //Product Main Data
            Route::resource('products_main_data', 'products_main_datacontroller');

            //Store Main Data
            Route::resource('store_main_data', 'store_main_datacontroller');
            Route::get('/ajax/products-search', [ProductsLookupController::class, 'search'])
                ->name('ajax.products.search');
            Route::get('/api/products/lookup', [\App\Http\Controllers\Api\ProductLookupController::class, '__invoke'])
                ->name('api.products.lookup');

            //Store program
            Route::get('/add_items', 'store_programcontroller@add_items')->name('add_items');
            Route::get('/storemovement', 'store_programcontroller@storemovement')->name('storemovement');
            // routes/web.php
            Route::get('storemovementdetails/{id}/show', 'store_programcontroller@show')->name('storemovementdetails.show');
            Route::get('storemovementdetails/{id}/edit', 'store_programcontroller@edit')->name('storemovementdetails.edit');
            Route::get('storemovementdetails/{id}/print', 'store_programcontroller@print')->name('storemovementdetails.print');
            Route::get('/opening_balance', 'store_programcontroller@opening_balance')->name('opening_balance');
            // routes/web.php
            Route::get('opening_balance/{id}/show', 'store_programcontroller@showopening_balance')->name('opening_balance.show');
            Route::get('opening_balance/{id}/edit', 'store_programcontroller@editopening_balance')->name('opening_balance.edit');
            Route::get('opening_balance/{id}/print', 'store_programcontroller@printopening_balance')->name('opening_balance.print');

            Route::get('/view_opening_balance', 'store_programcontroller@opening_balanceview')->name('view_opening_balance');
            Route::get('/transfer_substore', 'store_programcontroller@transfer_substore')->name('transfer_substore');
            // routes/web.php
            Route::get('opening_balance/{id}/show', 'store_programcontroller@showopening_balance')->name('opening_balance.show');
            Route::get('opening_balance/{id}/edit', 'store_programcontroller@editopening_balance')->name('opening_balance.edit');
            Route::get('opening_balance/{id}/print', 'store_programcontroller@printopening_balance')->name('opening_balance.print');

            Route::get('/view_opening_balance', 'store_programcontroller@opening_balanceview')->name('view_opening_balance');
            Route::get('/withdrawal_requests', 'store_programcontroller@withdrawal_requests')->name('withdrawal_requests');

            // Employee Item Requests
            Route::get('/issue_items_to_employee', 'store_requestmangmentcontroller@issue_items_to_employee')->name('issue_items_to_employee');
            Route::get('/create_request_employee', 'store_requestmangmentcontroller@create_request_employee')->name('create_request_employee');
            Route::get('edit_request_employee/{id}/employee_requestedit', 'store_requestmangmentcontroller@employee_requestedit')->name('edit_request_employee.employee_requestedit');
            Route::get('print_request_employee/{id}/print', 'store_requestmangmentcontroller@print')->name('print_request_employee.print');
            Route::get('show_request_employee/{id}/employee_requestshow', 'store_requestmangmentcontroller@employee_requestshow')->name('show_request_employee.employee_requestshow');

            // department Item Requests
            Route::get('/issue_items_to_department', 'store_requestdepartmentmangmentcontroller@issue_items_to_department')->name('issue_items_to_department');
            Route::get('/create_request_department', 'store_requestdepartmentmangmentcontroller@create_request_department')->name('create_request_department');
            Route::get('edit_request_department/{id}/department_requestedit', 'store_requestdepartmentmangmentcontroller@department_requestedit')->name('edit_request_department.department_requestedit');
            Route::get('print_request_department/{id}/print', 'store_requestdepartmentmangmentcontroller@print')->name('print_request_department.print');
            Route::get('show_request_department/{id}/department_requestshow', 'store_requestdepartmentmangmentcontroller@department_requestshow')->name('show_request_department.department_requestshow');

            // issue_items_to_store
            Route::get('/issue_items_to_store', 'store_requeststorecontroller@issue_items_to_store')->name('issue_items_to_store');
            Route::get('/create_request_to_store', 'store_requeststorecontroller@create_request_store')->name('create_request_store');
            Route::get('edit_request_to_store/{id}/store_requestedit', 'store_requeststorecontroller@store_requestedit')->name('edit_request_store.store_requestedit');
            Route::get('print_request_to_store/{id}/print', 'store_requeststorecontroller@print')->name('print_request_store.print');
            Route::get('show_request_to_store/{id}/store_requestshow', 'store_requeststorecontroller@store_requestshow')->name('show_request_store.store_requestshow');

            // issue_items_to_store
            Route::get('/request_issue_to_client', 'client_requeststorecontroller@issue_items_to_client')->name('issue_items_to_client');
            Route::get('/create_request_to_client', 'client_requeststorecontroller@create_request_client')->name('create_request_client');
            Route::get('edit_request_client/{id}/client_requestedit', 'client_requeststorecontroller@client_requestedit')->name('edit_request_client');
            Route::get('print_request_client/{id}/print', 'client_requeststorecontroller@print')->name('print_request_client');
            Route::get('show_request_client/{id}/client_requestshow', 'client_requeststorecontroller@client_requestshow')->name('show_request_client');

            // issue_items_to_store
            Route::get('/receive_withdraw_requests', 'receive_withdraw_requestscontroller@receive_withdraw_requests')->name('receive_withdraw_requests');
            // routes/web.php
            Route::get('client_request_process/{id}/client_request_process', 'receive_withdraw_requestscontroller@client_request_process')->name('client_request.process');
            Route::get('employee_request_process/{id}/employee_request_process', 'receive_withdraw_requestscontroller@employee_request_process')->name('employee_request.process');
            Route::get('department_request_process/{id}/department_request_process', 'receive_withdraw_requestscontroller@department_request_process')->name('department_request.process');
            Route::get('store_request_process/{id}/store_request_process', 'receive_withdraw_requestscontroller@store_request_process')->name('store_request.process');
            Route::get('view_request/{id}/view_request', 'receive_withdraw_requestscontroller@view_request')->name('view_request');
            Route::get('/print-request/{id}/{type}', 'receive_withdraw_requestscontroller@print_client_request')
                ->name('print_client_request');


            Route::get('/write_off_items', 'damaged_requestcontroller@damaged_request')->name('write_off_items');
            Route::get('/damaged_request_create', 'damaged_requestcontroller@create_damaged_request')->name('damaged_request.create');
            Route::get('damaged_request/{id}/show', 'damaged_requestcontroller@show')->name('damaged_request.show');


            //items requests
            Route::get('/new_item_request', 'store_programcontroller@new_item_request')->name('new_item_request');
            Route::get('items', 'create_item_requestcontroller@getItems')->name('create_item_request.items');
            Route::resource('create_item_request', 'create_item_requestcontroller')
                ->parameters(['create_item_request' => 'item_request']);

            //manage
            Route::resource('manage_item_request', 'manage_item_requestcontroller');

            // لجلب تفاصيل طلب بصيغة JSON
            Route::get(
                'manage_item_request/{manage_item_request}/details',
                'manage_item_requestcontroller@details'
            )->name('manage_item_request.details');



            // تغيير حالة بند الطلب (approve/reject)
            Route::post(
                'manage_item_request/{detail}/status',
                [\App\Http\Controllers\Supply_Chain\manage_item_requestcontroller::class, 'updateDetailStatus']
            )->name('manage_item_request.detail_status');

            //receiving_inspecting_items
            Route::get('/receiving_inspecting_page', 'store_programcontroller@receiving_inspecting')->name('receiving_inspecting_page');
            Route::resource('receiving_inspecting_items', 'receiving_inspecting_itemscontroller');
            Route::resource('manage_receiving_inspecting_items', 'manage_receiving_inspecting_itemscontroller');


            // Receiving & Inspecting
            Route::resource('receiving_inspecting', 'receiving_inspecting_itemscontroller')
                ->only(['index', 'create', 'store']);

            Route::get('receiving_inspecting/datatable', 'receiving_inspecting_itemscontroller@datatable')
                ->name('receiving_inspecting.datatable');

            Route::get('receiving_inspecting/available-pos', 'receiving_inspecting_itemscontroller@availablePOs')
                ->name('receiving_inspecting.available_pos');

            Route::get('receiving_inspecting/po/{po}/items', 'receiving_inspecting_itemscontroller@poItems')
                ->name('receiving_inspecting.po_items');

            Route::get('receiving_inspecting/suppliers', 'receiving_inspecting_itemscontroller@searchSuppliers')
                ->name('receiving_inspecting.suppliers');

            Route::get('receiving_inspecting/stores', 'receiving_inspecting_itemscontroller@searchStores')
                ->name('receiving_inspecting.stores');

            Route::get('receiving_inspecting/employees', 'receiving_inspecting_itemscontroller@searchEmployees')
                ->name('receiving_inspecting.employees');
            Route::get('receiving_inspecting/po/{po}/committee', 'receiving_inspecting_itemscontroller@poCommittee')
                ->name('receiving_inspecting.po_committee');
            Route::get('receiving_inspecting/{id}', 'receiving_inspecting_itemscontroller@show')
                ->name('receiving_inspecting.show');

            Route::delete('receiving_inspecting/{id}', 'receiving_inspecting_itemscontroller@destroy')
                ->name('receiving_inspecting.destroy');

            Route::get('receiving_inspecting/{id}/edit', 'receiving_inspecting_itemscontroller@edit')
                ->name('receiving_inspecting.edit');
            Route::put('receiving_inspecting/{id}', 'receiving_inspecting_itemscontroller@update')
                ->name('receiving_inspecting.update');
            // ====================== عضو اللجنة ======================
            // ضع datatable أولاً حتى لا يلتقطه مسار show
            Route::get(
                'manage_receiving_inspecting_approvals/datatable',
                [ApprovalController::class, 'datatable']
            )->name('manage_receiving_inspecting_approvals.datatable');

            // أكشنز الموافقة/الرفض مع تقييد الباراميتر أنه رقم
            Route::post(
                'manage_receiving_inspecting_approvals/{approval}/approve',
                [ApprovalController::class, 'approve']
            )->whereNumber('approval')->name('manage_receiving_inspecting_approvals.approve');

            Route::post(
                'manage_receiving_inspecting_approvals/{approval}/reject',
                [ApprovalController::class, 'reject']
            )->whereNumber('approval')->name('manage_receiving_inspecting_approvals.reject');

            // الريسورس (index, show) مع تغيير اسم الباراميتر الطويل إلى قصير
            Route::resource(
                'manage_receiving_inspecting_approvals',
                ApprovalController::class
            )->only(['index', 'show'])->parameters([
                'manage_receiving_inspecting_approvals' => 'approval',
            ])->where(['approval' => '[0-9]+']);
            Route::get('{locale}/receiving_inspecting/product_units', [\App\Http\Controllers\supply_chain\receiving_inspecting_itemscontroller::class, 'productUnits'])
                ->name('receiving_inspecting.product_units');


            //الجرد
            Route::prefix('inventory_audits')->name('inventory_audits.')->group(function () {
                // أساسية
                Route::get('/',                [inventoryauditcontroller::class, 'index'])->name('index');

                // إنشاء محضر (لازم قبل /{id})
                Route::get('/create',          [inventoryauditcontroller::class, 'create'])->name('create');   // requires store_id
                Route::post('/start',          [inventoryauditcontroller::class, 'start'])->name('start');     // يعطّل المخزن ويرجع redirect
                Route::post('/',               [inventoryauditcontroller::class, 'store'])->name('store');

                Route::post('/inventory-audits/reactivate-store', [inventoryauditcontroller::class, 'reactivateStore'])
                    ->name('reactivate_store');

                // AJAX helpers
                Route::get('/ajax/stores',                     [inventoryauditcontroller::class, 'ajaxStores'])->name('ajax.stores');
                Route::get('/ajax/products',                   [inventoryauditcontroller::class, 'ajaxProducts'])->name('ajax.products');
                Route::get('/ajax/categories',                 [inventoryauditcontroller::class, 'ajaxCategories'])->name('ajax.categories');
                Route::get('/ajax/products-by-category/{id}',  [inventoryauditcontroller::class, 'ajaxProductsByCategory'])->name('ajax.products_by_category');
                Route::get('/ajax/product-units/{product}',    [inventoryauditcontroller::class, 'ajaxProductUnits'])->name('ajax.product_units');
                Route::get('/ajax/balances/{store}',           [inventoryauditcontroller::class, 'ajaxBalancesByStore'])->name('ajax.balances_by_store');
                Route::get('/ajax/employees',                  [inventoryauditcontroller::class, 'ajaxEmployees'])->name('ajax.employees');

                Route::get('/ajax/balances/by-products/{storeId}', [inventoryauditcontroller::class, 'ajaxBalancesByProducts'])
                    ->name('ajax.balances_by_products');

                Route::get('/ajax/balances-paged/{store}', [inventoryauditcontroller::class, 'ajaxBalancesPaged'])
                    ->name('ajax.balances_paged');

                Route::get('/ajax/product-units-bulk', [inventoryauditcontroller::class, 'ajaxProductUnitsBulk'])
                    ->name('ajax.product_units_bulk');

                // صفحات لاحقة
                Route::get('/{id}/edit',       [inventoryauditcontroller::class, 'edit'])->name('edit');
                Route::get('/{id}/adjust',     [inventoryauditcontroller::class, 'adjust'])->name('adjust');

                // === المسار المطلوب للتحديث (جديد) ===
                Route::match(['put', 'patch'], '/{id}', [inventoryauditcontroller::class, 'update'])->name('update');
                // ================================
                Route::get('/{id}/print',  [inventoryauditcontroller::class, 'print'])->name('print');
                //تسوية
                Route::post('/{id}/settle', [inventoryauditcontroller::class, 'settle'])
                    ->name('settle');
                // لازم تيجي في الآخر
                Route::get('/{id}',            [inventoryauditcontroller::class, 'show'])->name('show');
                Route::delete('/{id}',         [inventoryauditcontroller::class, 'destroy'])->name('destroy');
                Route::post('/{id}/prepare-edit', [inventoryauditcontroller::class, 'prepareEdit'])->name('prepare_edit');
            });
            Route::prefix('edit_item_expiry')->name('edit_item_expiry.')->group(function () {
                Route::get('/',              [edit_item_expirycontroller::class, 'index'])->name('index');
                Route::get('/ajax/list',     [edit_item_expirycontroller::class, 'ajaxList'])->name('ajax.list');
                Route::post('/update',       [edit_item_expirycontroller::class, 'update'])->name('update');
            });

            //print barcode
            Route::resource('print_barcode', 'print_barcodecontroller');

            //adjust_balance
            Route::prefix('adjust_balance/ajax')->name('adjust_balance.ajax.')->group(function () {
                Route::get('stores',            [adjust_balancecontroller::class, 'ajaxStores'])->name('stores');
                Route::get('products_by_store', [adjust_balancecontroller::class, 'ajaxProductsByStore'])->name('products_by_store');
                Route::get('product_units',     [adjust_balancecontroller::class, 'ajaxProductUnitsBulk'])->name('product_units_bulk');
                Route::get('batches',           [adjust_balancecontroller::class, 'ajaxBatches'])->name('batches');
                Route::get('current_balance',   [adjust_balancecontroller::class, 'ajaxCurrentBalance'])->name('current_balance');
                Route::get('next_code',         [adjust_balancecontroller::class, 'ajaxNextCode'])->name('next_code');
                Route::get('list',              [adjust_balancecontroller::class, 'ajaxList'])->name('list');
            });

            Route::resource('adjust_balance', 'adjust_balancecontroller');

            // routes/web.php

            Route::prefix('dev')->name('dev.')->group(function () {
                Route::get('/unit-conversion', [UnitConversionTestController::class, 'index'])->name('unitconv.index');
                Route::post('/unit-conversion', [UnitConversionTestController::class, 'calc'])->name('unitconv.calc');
                Route::get('/unit-conversion/units/{product}', [UnitConversionTestController::class, 'unitsJson'])
                    ->name('unitconv.units'); // لإرجاع وحدات الصنف JSON

            });

            //return_items
            Route::resource('return_items', 'return_itemscontroller');

            //return_items_to_supplier
            Route::resource('return_to_supplier', 'return_to_suppliercontroller');
            Route::prefix('return_to_supplier/ajax')->group(function () {
                Route::get('suppliers', [return_to_suppliercontroller::class, 'ajaxSuppliers']);
                Route::get('stores',    [return_to_suppliercontroller::class, 'ajaxStores']);
                Route::get('invoices',  [return_to_suppliercontroller::class, 'ajaxInvoices']);
                Route::get('products',  [return_to_suppliercontroller::class, 'ajaxProducts']);
                Route::get('batches',   [return_to_suppliercontroller::class, 'ajaxBatches']);
                Route::get('units',     [return_to_suppliercontroller::class, 'ajaxUnits']);
                Route::get('invoice_items', [return_to_suppliercontroller::class, 'ajaxInvoiceItems']);
            });
            // return_direct_to_store
            Route::resource('return_direct_to_store', 'return_direct_to_storecontroller');

            Route::prefix('return_direct_to_store/ajax')->group(function () {
                // جلب أول id عبر الكود (للاستخدام في المودالات)
                Route::get('first-id-by-code', [\App\Http\Controllers\supply_chain\return_direct_to_storecontroller::class, 'firstIdByCode']);

                // ✅ مخازن مبنية على store_transfers:
                // المخازن التي استلمت تحويلات (مرشحة كـ "مرتجع منه")
                Route::get('store-froms', [\App\Http\Controllers\supply_chain\return_direct_to_storecontroller::class, 'ajaxStoreFroms']);
                // شركاء المخزن الأول (مرشحة كـ "مرتجع إليه")
                Route::get('store-tos',   [\App\Http\Controllers\supply_chain\return_direct_to_storecontroller::class, 'ajaxStoreTos']);

                // باقي الـ AJAX المستخدمة في الصفحة
                Route::get('units',     [\App\Http\Controllers\supply_chain\return_direct_to_storecontroller::class, 'ajaxUnits']);
                Route::get('transfers', [\App\Http\Controllers\supply_chain\return_direct_to_storecontroller::class, 'ajaxTransfers']);
                Route::get('batches',   [\App\Http\Controllers\supply_chain\return_direct_to_storecontroller::class, 'ajaxBatches']);
            });
            //return_from_section
            Route::resource('return_from_section', 'return_from_sectioncontroller');

            // AJAX
            Route::prefix('return_from_section/ajax')->group(function () {
                // لالتقاط أول id بالكود (للعرض/الحذف)
                Route::get('first-id-by-code', [\App\Http\Controllers\supply_chain\return_from_sectioncontroller::class, 'firstIdByCode']);

                // محركات البحث
                Route::get('departments', [\App\Http\Controllers\supply_chain\return_from_sectioncontroller::class, 'ajaxDepartments']); // الاسم الذي يناديه الفيو
                Route::get('sections',    [\App\Http\Controllers\supply_chain\return_from_sectioncontroller::class, 'ajaxDepartments']); // ألياس اختياري للتوافق

                Route::get('stores',   [\App\Http\Controllers\supply_chain\return_from_sectioncontroller::class, 'ajaxStores']);
                Route::get('units',    [\App\Http\Controllers\supply_chain\return_from_sectioncontroller::class, 'ajaxUnits']);

                // المصادر العملية
                Route::get('requests', [\App\Http\Controllers\supply_chain\return_from_sectioncontroller::class, 'ajaxRequests']);
                Route::get('batches',  [\App\Http\Controllers\supply_chain\return_from_sectioncontroller::class, 'ajaxBatches']);
            });
            //return_from_employee
            // صفحة ارتجاع من موظف
            Route::resource('return_from_employee', 'return_from_employeecontroller');
            Route::prefix('return_from_employee/ajax')->group(function () {
                Route::get('first-id-by-code', [\App\Http\Controllers\supply_chain\return_from_employeecontroller::class, 'firstIdByCode']);
                Route::get('employees', [\App\Http\Controllers\supply_chain\return_from_employeecontroller::class, 'ajaxEmployees']);
                Route::get('stores',    [\App\Http\Controllers\supply_chain\return_from_employeecontroller::class, 'ajaxStores']);
                Route::get('units',     [\App\Http\Controllers\supply_chain\return_from_employeecontroller::class, 'ajaxUnits']);
                Route::get('requests',  [\App\Http\Controllers\supply_chain\return_from_employeecontroller::class, 'ajaxRequests']);
                Route::get('batches',   [\App\Http\Controllers\supply_chain\return_from_employeecontroller::class, 'ajaxBatches']);
            });
            // return_from_client
            // resource
            Route::resource('return_from_client', \App\Http\Controllers\supply_chain\return_from_clientcontroller::class);

            // AJAX
            Route::prefix('return_from_client/ajax')->group(function () {
                Route::get('first-id-by-code', [\App\Http\Controllers\supply_chain\return_from_clientcontroller::class, 'firstIdByCode']);

                Route::get('clients',  [\App\Http\Controllers\supply_chain\return_from_clientcontroller::class, 'ajaxClients']);
                Route::get('stores',   [\App\Http\Controllers\supply_chain\return_from_clientcontroller::class, 'ajaxStores']);
                Route::get('units',    [\App\Http\Controllers\supply_chain\return_from_clientcontroller::class, 'ajaxUnits']);
                Route::get('requests', [\App\Http\Controllers\supply_chain\return_from_clientcontroller::class, 'ajaxRequests']);
                Route::get('batches',  [\App\Http\Controllers\supply_chain\return_from_clientcontroller::class, 'ajaxBatches']);
            });



            Route::get('/return_to_store', [return_itemscontroller::class, 'return_to_store'])->name('return_to_store');

            //reports
            Route::resource('store_movements_report', \App\Http\Controllers\supply_chain\store_movements_reportcontroller::class);
            Route::resource('warehouse_balance_report', \App\Http\Controllers\supply_chain\warehouse_balance_reportcontroller::class);
            Route::resource('warehouse_keeper_report', \App\Http\Controllers\supply_chain\warehouse_keeper_reportcontroller::class);
            Route::resource('item_consumption_rate_report', \App\Http\Controllers\supply_chain\item_consumption_rate_reportcontroller::class);
            Route::resource('item_card_report', \App\Http\Controllers\supply_chain\item_card_reportcontroller::class);
            Route::resource('item_groups_report', \App\Http\Controllers\supply_chain\item_groups_reportcontroller::class);
            Route::resource('item_data_report', \App\Http\Controllers\supply_chain\item_data_reportcontroller::class);
            Route::resource('expired_item_report', \App\Http\Controllers\supply_chain\expired_item_reportcontroller::class);
            Route::resource('transfer_item_report', \App\Http\Controllers\supply_chain\transfer_item_reportcontroller::class);
            Route::resource('near_expiry_item_report', \App\Http\Controllers\supply_chain\near_expiry_item_reportcontroller::class);
            Route::resource('damaged_items_report', \App\Http\Controllers\supply_chain\damaged_items_reportcontroller::class);
            Route::resource('current_stock_report', \App\Http\Controllers\supply_chain\current_stock_reportcontroller::class);
            Route::resource('item_additions_report', \App\Http\Controllers\supply_chain\item_additions_reportcontroller::class);

        });
    },
);

Auth::routes();
//Auth::routes(['register' => false]);
