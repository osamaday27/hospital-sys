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
        //medical_record
        Route::group(['namespace' => 'administrative\medical_record'], function () {
            Route::resource('medical_record', 'medical_recordcontroller');
            // New route for getting medical_record
            Route::get('/get-medical_record', 'medical_recordcontroller@getmedical_record')->name('get.medical_record');
            Route::get('medical_record/{medical_record}/edit', 'medical_recordcontroller@edit')->name('medical_record.edit');
            Route::get('medical_record/{medical_record}/show', 'medical_recordcontroller@show')->name('medical_record.show');
            Route::delete('/medical_record/{id}', [medical_recordcontroller::class, 'destroy'])->name('medical_record.destroy');
            Route::resource('medical_record_ajax', 'medical_recordajaxcontroller');
            // في ملف الويب روتر
            Route::post('/patients/import','medical_recordcontroller@import')->name('patients.import');
        });
    },
);

Auth::routes();
//Auth::routes(['register' => false]);