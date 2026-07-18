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
        Route::group(['namespace' => 'general'], function () {
            //Place settings
            Route::resource('places_settings', 'place_settingsController');
            //countries
            Route::resource('countries', 'CountriesController');
            //city
            Route::get('/city/{id}', 'citycontroller@getgovernment');
            Route::resource('city', 'citycontroller');
            Route::get('/get_city/{id}', 'areaController@get_city');
            //government
            Route::resource('government', 'governmentController');
            Route::get('/get_governmentes/{id}', 'citycontroller@get_government');
            //area
            Route::get('/area/{id}', 'areaController@getcity');
            Route::resource('area', 'areaController');
            Route::get('/get_area/{id}', 'areaController@get_area');
            //settings_type settings
            Route::resource('settings_type', 'settings_typeController');
            // application_setting
            Route::resource('settings', 'generalcontroller');
            //currencies settings
            Route::resource('currencies', 'currenciesController');
            //nationalities settings
            Route::resource('nationalities_settings', 'nationalities_settingsController');
               //nationalities settings
            Route::resource('jop_settings', 'jopcontroller');
               //nationalities settings
            Route::resource('how_to_identify_place_settings', 'how_to_identify_placecontroller');
        });
        //namedictionary
        Route::group(['namespace' => 'general'], function () {
            Route::resource('namedictionary', 'namedictionarycontroller');
            // New route for getting namedictionary
            Route::get('/get-namedictionary', 'namedictionarycontroller@getnamedictionary')->name('get.namedictionary');
            Route::get('namedictionary/{namedictionary}/edit', 'namedictionarycontroller@edit')->name('namedictionary.edit');
            Route::delete('/namedictionary/{id}', [namedictionarycontroller::class, 'destroy'])->name('namedictionary.destroy');
            Route::post('/translate-names', 'namedictionarycontroller@translateNames')->name('namedictionary.translateNames');
            Route::get('/patientnamereview', 'namedictionarycontroller@patientnamereview')->name('patientnamereview');

        });
        //building_settings
        Route::group(['namespace' => 'general'], function () {
            Route::resource('building_settings', 'building_settingscontroller');
            // New route for getting namedictionary
            Route::get('/get-building_settings', 'building_settingscontroller@getbuilding_settings')->name('get.building_settings');
            Route::get('building_settings/{building_settings}/edit', 'building_settingscontroller@edit')->name('building_settings.edit');
            Route::delete('building_settings/{id}', 'building_settingscontroller@destroy')->name('building_settings.destroy');
        });
        //building_settings
        Route::group(['namespace' => 'general'], function () {
            Route::resource('floors_settings', 'floors_settingscontroller');
            // New route for getting namedictionary
            Route::get('/get-floors_settings', 'floors_settingscontroller@getfloors_settings')->name('get.floors_settings');
            route::get('floors_settings/create/{id}', 'floors_settingscontroller@create')->name('floors_settings.create');
            Route::get('floors_settings/{floors_settings}/edit', 'floors_settingscontroller@edit')->name('floors_settings.edit');
            Route::delete('floors_settings/{id}', 'floors_settingscontroller@destroy')->name('floors_settings.destroy');
        });
         //specialties_settings
        Route::group(['namespace' => 'general'], function () {
            Route::resource('specialties_settings', 'specialtiessettingscontroller');
            // New route for getting namedictionary
            Route::get('/get-specialties', 'specialtiessettingscontroller@getspecialties')->name('get.specialties');
            route::get('specialties_settings/create/{id}', 'specialtiessettingscontroller@create')->name('specialties_settings.create');
            Route::get('specialties_settings/{specialty}/edit', 'specialtiessettingscontroller@edit')->name('specialties_settings.edit');
            Route::delete('specialties_settings/{id}', 'specialtiessettingscontroller@destroy')->name('specialties_settings.destroy');
        });
    },
);

Auth::routes();
//Auth::routes(['register' => false]);