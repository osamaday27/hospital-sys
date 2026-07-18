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
        //lockers
        Route::group(['namespace' => 'finance\lockers'], function () {
            Route::resource('lockers_settings', 'lockers_settingscontroller');
            Route::resource('lockers_programs', 'lockers_programscontroller');
            Route::resource('locker_c_settings', 'lockers_settings_mangmentcontroller');
          
            Route::get('/bank-settings', 'lockers_settings_mangmentcontroller@bank_settings')->name('bank_settings');
            Route::get('/bank-machines', 'lockers_settings_mangmentcontroller@bank_machines')->name('bank_machines');

            Route::resource('cancellationreason', 'cancellationreasoncontroller');
            Route::resource('reasonspartialpayment', 'reasonspartialpaymentcontroller');
            Route::resource('locker_desktop',        'lockerdesktopcontroller');

            Route::get('/get-unpaid_receipts', 'lockerscontroller@unpaid_receipts')->name('get.unpaid_receipts');
            Route::get('/requests_cancel_receipts', 'cancellationrequestcontroller@cancellationrequestmangment')->name('requests_cancel_receipts');
            Route::get('/discount_requests', 'discountrequestcontroller@discount_requests')->name('discount_requests');
            Route::get('/delivering_sub_lockers_main_lockers', 'lockerdesktopcontroller@delivering_sub_lockers_main_lockers')->name('delivering_sub_lockers_main_lockers');


            Route::get('/finance/cancellation-request/{invoice_id}', 'cancellationrequestcontroller@cancellationrequest')->name('finance.cancellation-request');
            Route::get('/finance/discountrequest/{invoice_id}', 'discountrequestcontroller@discountrequest')->name('finance.discountrequest');
            
            Route::get('/finance/invoicepayment/{invoice_id}', 'invoicepaymentcontroller@invoicepayment')->name('finance.invoicepayment');
            Route::get('/finance/invoicedetails/{invoice_id}', 'invoicepaymentcontroller@invoicedetails')->name('finance.invoicedetails');
            Route::get('/finance/invoiceprint/{invoice_id}', 'invoicepaymentcontroller@invoice_payment_print')->name('finance.invoice_payment_print');
            Route::get('/finance/invoiceprintthermal/{invoice_id}', 'invoicepaymentcontroller@invoice_payment_printthermal')->name('finance.invoice_payment_printthermal');
            

        });
    },
);

Auth::routes();
//Auth::routes(['register' => false]);