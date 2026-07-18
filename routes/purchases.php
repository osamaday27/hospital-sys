<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\purchases\purchases_system_settingscontroller;
use App\Http\Controllers\purchases\purchase_requisitionscontroller;
use App\Http\Controllers\purchases\purchase_ordercontroller;
use App\Http\Controllers\purchases\price_quotationscontroller;
use Illuminate\Http\Request;
use App\Models\supply_chain\productdata;

Auth::routes(['verify' => true]);

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'auth', 'verified'],
], function () {
    Route::group(['namespace' => 'purchases'], function () {
        Route::resource('purchases_settings', 'purchases_settingscontroller');
        Route::resource('purchases_programs', 'purchases_programscontroller');
        Route::resource('purchases_reports', 'purchases_reportscontroller');
        //price_quotations
        Route::resource('price_quotations', 'price_quotationscontroller');
        Route::get('/{id}/print', [price_quotationscontroller::class, 'print'])->name('price_quotations.print');
        // عرض قائمة عروض الأسعار للطلب المختار
        Route::get('price_quotations', [price_quotationscontroller::class, 'index'])->name('price_quotations.index');
        // إظهار بيانات عرض السعر (للـ AJAX)
        Route::get('price_quotations/{id}', [price_quotationscontroller::class, 'show'])->name('price_quotations.show');


        // 1) مسار حفظ الإعدادات العُليا
        Route::post(
            'purchases_system_settings/saveSettings',
            [purchases_system_settingscontroller::class, 'saveSettings']
        )->name('purchases_system_settings.saveSettings');

        // 2) CRUD بنود التقييم
        Route::get('purchases_system_settings/evaluation-items', [purchases_system_settingscontroller::class, 'evaluationItemsList'])
            ->name('purchases_system_settings.evaluationItemsList');
        Route::post('purchases_system_settings/evaluation-items', [purchases_system_settingscontroller::class, 'evaluationItemsStore'])
            ->name('purchases_system_settings.evaluationItemsStore');
        Route::get('purchases_system_settings/evaluation-items/{id}', [purchases_system_settingscontroller::class, 'evaluationItemsShow'])
            ->name('purchases_system_settings.evaluationItemsShow');
        Route::put('purchases_system_settings/evaluation-items/{id}', [purchases_system_settingscontroller::class, 'evaluationItemsUpdate'])
            ->name('purchases_system_settings.evaluationItemsUpdate');
        Route::delete('purchases_system_settings/evaluation-items/{id}', [purchases_system_settingscontroller::class, 'evaluationItemsDestroy'])
            ->name('purchases_system_settings.evaluationItemsDestroy');

        // 3) CRUD أماكن التسليم
        Route::get('purchases_system_settings/delivery-places', [purchases_system_settingscontroller::class, 'deliveryPlacesList'])
            ->name('purchases_system_settings.deliveryPlacesList');
        Route::post('purchases_system_settings/delivery-places', [purchases_system_settingscontroller::class, 'deliveryPlacesStore'])
            ->name('purchases_system_settings.deliveryPlacesStore');
        Route::get('purchases_system_settings/delivery-places/{id}', [purchases_system_settingscontroller::class, 'deliveryPlacesShow'])
            ->name('purchases_system_settings.deliveryPlacesShow');
        Route::put('purchases_system_settings/delivery-places/{id}', [purchases_system_settingscontroller::class, 'deliveryPlacesUpdate'])
            ->name('purchases_system_settings.deliveryPlacesUpdate');
        Route::delete('purchases_system_settings/delivery-places/{id}', [purchases_system_settingscontroller::class, 'deliveryPlacesDestroy'])
            ->name('purchases_system_settings.deliveryPlacesDestroy');

        // 4) CRUD أسباب الإغلاق
        Route::get('purchases_system_settings/closure-reasons', [purchases_system_settingscontroller::class, 'closureReasonsList'])
            ->name('purchases_system_settings.closureReasonsList');
        Route::post('purchases_system_settings/closure-reasons', [purchases_system_settingscontroller::class, 'closureReasonsStore'])
            ->name('purchases_system_settings.closureReasonsStore');
        Route::get('purchases_system_settings/closure-reasons/{id}', [purchases_system_settingscontroller::class, 'closureReasonsShow'])
            ->name('purchases_system_settings.closureReasonsShow');
        Route::put('purchases_system_settings/closure-reasons/{id}', [purchases_system_settingscontroller::class, 'closureReasonsUpdate'])
            ->name('purchases_system_settings.closureReasonsUpdate');
        Route::delete('purchases_system_settings/closure-reasons/{id}', [purchases_system_settingscontroller::class, 'closureReasonsDestroy'])
            ->name('purchases_system_settings.closureReasonsDestroy');

        // 5) في النهاية تعريف الـ resource (مع استبعاد show حتى لا يتضارب)
        Route::resource('purchases_system_settings', 'purchases_system_settingscontroller')
            ->except(['show']);

        //purchase requisitions
        // موارد CRUD لطلبات الاحتياج
        Route::resource('purchase_requisitions', purchase_requisitionscontroller::class)->names([
            'index' => 'purchase_requisitions.index',
            'create' => 'purchase_requisitions.create',
            'store' => 'purchase_requisitions.store',
            'show' => 'purchase_requisitions.show',
            'edit' => 'purchase_requisitions.edit',
            'update' => 'purchase_requisitions.update',
            'destroy' => 'purchase_requisitions.destroy',
        ]);
        Route::post('/purchase_requisitions/attachments', [purchase_requisitionscontroller::class, 'attachments'])
            ->name('purchase_requisitions.attachments');

        Route::post('purchase_requisitions/delete-attachment', [purchase_requisitionscontroller::class, 'deleteAttachment'])->name('purchase_requisitions.delete_attachment');
        Route::get('purchase_requisitions/{id}/print', [purchase_requisitionscontroller::class, 'print'])
            ->name('purchase_requisitions.print');

 //supply_requests_report
    Route::resource('supply_requests_report', 'supply_requests_reportcontroller');
    Route::resource('late_delivery_items', 'late_delivery_itemscontroller');
    Route::resource('most_purchased_items', 'most_purchased_itemscontroller');
    Route::resource('supply_orders_report', 'supply_orders_reportcontroller');

    });

    Route::resource('purchase_order', purchase_ordercontroller::class)->names([
        'index' => 'purchase_order.index',
        'create' => 'purchase_order.create',
        'store' => 'purchase_order.store',
        'show' => 'purchase_order.show',
        'edit' => 'purchase_order.edit',
        'update' => 'purchase_order.update',
        'destroy' => 'purchase_order.destroy',
    ]);

    Route::get('purchase_order/{id}/print', [purchase_ordercontroller::class, 'print'])
        ->name('purchase_order.print');

    Route::get('price_quotations/actions/compare', [price_quotationscontroller::class, 'compare'])
        ->name('price_quotations.compare');
    Route::prefix('ajax')->group(function () {
        Route::get(
            'purchase_requisitions/items-by-store',
            [purchase_requisitionscontroller::class, 'itemsByStore']
        )->name('purchase_requisitions.items_by_store');





    });





});

Auth::routes();

//Auth::routes(['register' => false]);
