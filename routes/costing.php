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
        //costing
        Route::group(['namespace' => 'finance\costing'], function () {
            Route::resource('costing_settings', 'costing_settingscontroller');
            Route::resource('costing_programs', 'costing_programscontroller');
            Route::get('/cost_centers', 'costing_settingscontroller@cost_centers')->name('cost_centers');
            Route::get('/overhead_pools', 'costing_settingscontroller@overhead_pools')->name('overhead_pools');
            Route::get('/overhead_allocation', 'costing_settingscontroller@overhead_allocation')->name('overhead_allocation');
            Route::get('/resource_rates', 'costing_settingscontroller@resource_rates')->name('resource_rates');
            Route::get('/service_boms', 'costing_settingscontroller@service_boms')->name('service_boms');
            Route::get('/costing_run_wizard', 'costing_programscontroller@costing_run_wizard')->name('costing_run_wizard');
            Route::get('/costing_batches', 'costing_programscontroller@costing_batches')->name('costing_batches');
            Route::get('/costing_adjustments', 'costing_programscontroller@costing_adjustments')->name('costing_adjustments');
            Route::get('/costing_posting', 'costing_programscontroller@costing_posting')->name('costing_posting');
            Route::get('/cost_center_linking', 'costing_settingscontroller@cost_center_linking')->name('cost_center_linking');

        
           
        });
    },
);

Auth::routes();
//Auth::routes(['register' => false]);