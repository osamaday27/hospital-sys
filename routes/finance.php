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
        //finance
        Route::group(['namespace' => 'finance\pricing_management'], function () {
            Route::resource('pricing_management', 'pricing_managementcordcontroller');
            Route::resource('service_management_level1', 'servicemanagementlevel1cordcontroller');
            Route::resource('service_management_level2', 'servicemanagementlevel2cordcontroller');
            Route::resource('service_management_level3', 'servicemanagementlevel3cordcontroller');
            Route::resource('price_lists', 'pricelistcontroller');
            Route::resource('pricing_of_services', 'pricing_of_servicescontroller');
            Route::get('pricing_of_services/{id}/create', 'pricing_of_servicescontroller@create')->name('pricing_of_services.create');

            // New route for getting  service level
            Route::get('/get-servicelevel1', 'servicemanagementlevel1cordcontroller@getservicelevel1')->name('get.servicelevel1');
            Route::get('/get-servicelevel2', 'servicemanagementlevel2cordcontroller@getservicelevel2')->name('get.servicelevel2');
            Route::get('/get-servicelevel3', 'servicemanagementlevel3cordcontroller@getservicelevel3')->name('get.servicelevel3');
            Route::get('/get-pricelists', 'pricelistcontroller@getpricelists')->name('get.pricelists');
            Route::get('/get-pricing_of_services', 'pricing_of_servicescontroller@getpricing_of_services')->name('get.pricing_of_services');
            Route::post('/update-service-price', 'pricing_of_servicescontroller@update')->name('get.update-service-price');
        });

        Route::group(['namespace' => 'finance\contracts_management'], function () {
            Route::resource('contract_management', 'contracts_managementcardcontroller');
            Route::resource('management_contract_frist_level', 'contract_frist_levelcontroller');
            Route::get('/get-contract_frist_level', 'contract_frist_levelcontroller@getcontract_frist_level')->name('get.contract_frist_level');

            Route::resource('management_contract_second_level', 'contract_second_levelcontroller');
            Route::get('/get-contract_second_level', 'contract_second_levelcontroller@getcontract_second_level')->name('get.contract_second_level');
            Route::get('management_contract_second_level/{id}/creatdetails', 'contract_second_levelcontroller@creatdetails')->name('management_contract_second_level.creatdetails');
            Route::get('/services/list', 'contract_second_levelcontroller@getServices')->name('services.list');
            Route::match(['post', 'put'], 'storeOrUpdate', 'contract_second_levelcontroller@storeOrUpdate')->name('management_contract_second_level.storeOrUpdate');

            Route::resource('management_contract_last_level', 'contract_last_levelcontroller');
            Route::get('/get-contract_last_level', 'contract_last_levelcontroller@getcontract_last_level')->name('get.contract_last_level');
            Route::get('management_contract_last_level/{id}/creatdetails', 'contract_last_levelcontroller@creatdetails')->name('management_contract_last_level.creatdetails');
            Route::get('/services/list', 'contract_last_levelcontroller@getServices')->name('services.list');
            Route::match(['post', 'put'], 'storeOrUpdatel', 'contract_last_levelcontroller@storeOrUpdatel')->name('management_contract_last_level.storeOrUpdatel');
            Route::get('management_contract_last_level/{id}/financialsettings', 'contract_last_levelcontroller@financialsettings')->name('management_contract_last_level.financialsettings');
            Route::get('management_contract_last_level/{id}/financialsettings', 'contract_last_levelcontroller@financialsettings')->name('management_contract_last_level.financialsettings');
            Route::get('/surgeryfinancialsettings/{contract_id}/{service_id}', 'contract_last_levelcontroller@surgeryfinancialsettings')->name('surgeryfinancialsettings');
        });
        Route::group(['namespace' => 'finance\patient_accounts'], function () {
            Route::resource('patient_accounts', 'patient_accounts_programscontroller');
            Route::get('finance/{id}/openAdvancePayment', 'patient_accounts_programscontroller@openAdvancePayment')->name('finance.openAdvancePayment');
            Route::get('finance/{id}/surgical_invoice', 'patient_accounts_programscontroller@surgical_invoice')->name('finance.surgical_invoice');

           
        });
    },
);

Auth::routes();
//Auth::routes(['register' => false]);