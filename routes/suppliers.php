<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Auth::routes(['verify' => true]);

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'auth', 'verified'],
    ],
    function () {
        //suppliers
        Route::group(['namespace' => 'suppliers'], function () {
            Route::resource('suppliers_settings', 'suppliers_settingscontroller');
            Route::resource('suppliers_program', 'suppliers_programcontroller');
            Route::get('service_invoices', 'suppliers_programcontroller@service_invoices')->name('service_invoices');
            Route::get('serviceinvoicesindex', 'suppliers_programcontroller@serviceinvoicesindex')->name('serviceinvoicesindex');
            Route::resource('supplier_invoices', 'ServiceInvoicesController');
            Route::get('supplier-invoices/{id}/edit', 'ServiceInvoicesController@edit')->name('supplier_invoices.edit');
            Route::get('serviceinvoicesindex-payment/{id}/payment', 'ServiceInvoicesController@payment')->name('supplier_invoices.payment');
            Route::get('serviceinvoicesindex-show/{id}/show', 'ServiceInvoicesController@show')->name('supplier_invoices.show');
            Route::get('serviceinvoicesindex-print/{id}/print', 'ServiceInvoicesController@print')->name('supplier_invoices.print');

            Route::resource('warehouse_invoices', 'proudctInvoicescontroller');
            Route::get('warehouse_invoices/{id}/edit', 'proudctInvoicescontroller@edit')->name('warehouse_invoices.edit');
            Route::get('warehouse_invoices-payment/{id}/payment', 'proudctInvoicescontroller@payment')->name('warehouse_invoices.payment');
            Route::get('warehouse_invoices-show/{id}/show', 'proudctInvoicescontroller@show')->name('warehouse_invoices.show');
            Route::get('warehouse_invoices-print/{id}/print', 'proudctInvoicescontroller@print')->name('warehouse_invoices.print');
            Route::get('warehouse_invoices-create', 'proudctInvoicescontroller@create')->name('store_invoices.create');
            Route::get('supplier_notifications', 'suppliers_programcontroller@supplier_notifications')->name('supplier_notifications');




            Route::resource('suppliers_accounts', 'suppliers_accountscontroller');
            Route::get('suppliers_accounts/{id}/edit', 'suppliers_accountscontroller@edit')->name('suppliers_accounts.edit');
            Route::get('suppliers_accounts-payment/{id}/payment', 'suppliers_accountscontroller@payment')->name('suppliers_accounts.payment');
            Route::get('suppliers_accounts-details/{id}/details', 'suppliers_accountscontroller@details')->name('suppliers_accounts.details');
            Route::get('suppliers_accounts-create', 'suppliers_accountscontroller@create')->name('suppliers_accounts.create');




            Route::resource('suppliers_merge', 'suppliers_mergecontroller');
            Route::get('suppliers_mergelog', 'suppliers_mergecontroller@mergeslog')->name('mergeslog');



            Route::resource('suppliers_reports', 'suppliers_reportscontroller');

            //suppliers data
            Route::resource('suppliers_data', 'suppliers_datacontroller');

            //suppliers data
            Route::resource('suppliers_groups', 'suppliers_groupscontroller');

            //Suppliers Company
            Route::resource('suppliers_company', 'suppliers_companycontroller');
            Route::get('suppliers_company/get-governments/{country_id}', [App\Http\Controllers\suppliers\suppliers_companycontroller::class, 'getGovernments'])->name('suppliers_company.get_governments');

            Route::get('suppliers_company/get-cities/{government_id}', [App\Http\Controllers\suppliers\suppliers_companycontroller::class, 'getCities'])->name('suppliers_company.get_cities');
            // suppliers services
            Route::resource('suppliers_services', 'suppliers_servicescontroller');

            // suppliers general settings&notifaction
            // صفحة العرض فقط
            Route::resource('suppliers_general_settings', 'generalsettingscontroller')
                ->only(['index'])
                ->names(['index' => 'suppliers_general_settings.index']);

            // حفظ/تحديث الإعدادات العامة
            Route::post('suppliers_general_settings/save-general', 'generalsettingscontroller@saveGeneral')->name('suppliers_general_settings.saveGeneral');

            // جلب قائمة الإشعارات (DataTable AJAX)
            Route::get('suppliers_general_settings/list-notifications', 'generalsettingscontroller@listNotifications')->name('suppliers_general_settings.listNotifications');

            // إنشاء إشعار جديد
            Route::post('suppliers_general_settings/notification', 'generalsettingscontroller@saveNotification')->name('suppliers_general_settings.saveNotification');

            // جلب بيانات إشعار للتعديل
            Route::get('suppliers_general_settings/notification/{notification}', 'generalsettingscontroller@showNotification')->name('suppliers_general_settings.showNotification');

            // تحديث إشعار موجود
            Route::put('suppliers_general_settings/notification/{notification}', 'generalsettingscontroller@saveNotification');

            // حذف إشعار (soft delete)
            Route::delete('suppliers_general_settings/notification/{notification}', 'generalsettingscontroller@deleteNotification')->name('suppliers_general_settings.deleteNotification');

            //supplier_opening_balances
            Route::resource('supplier_opening_balances', 'supplier_opening_balancescontroller');

            //supplier_notifications
            Route::resource('supplier_alerts', 'supplieralertscontroller');

            //suppliers reports
            Route::resource('supplier_payment_report', 'supplier_payment_reportcontroller');


        });
    },
);

Auth::routes();
//Auth::routes(['register' => false]);
