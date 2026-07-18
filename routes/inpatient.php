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
        //inpatient
        Route::group(['namespace' => 'administrative\inpatient'], function () {
            Route::resource('inpatient_settings', 'inpatient_settingscontroller');
            Route::resource('inpatient_programs', 'inpatient_programscontroller');
            Route::resource('inpatient_booking', 'inpatient_bookingcontroller');
            Route::resource('beds_map', 'hospitalmapcontroller');
            Route::get('/get_patient_visit', 'inpatient_bookingcontroller@getpatientvisit')->name('get.patient.visits');

            // get data booking
            Route::get('/getRoomsByNursingStation', 'get_data_bookingcontroller@getRoomsByNursingStation')->name('getRoomsByNursingStation');
            Route::get('/getBedsByRoom', 'get_data_bookingcontroller@getBedsByRoom')->name('getBedsByRoom');
            Route::get('/getentry_sources', 'get_data_bookingcontroller@getentrysources')->name('getentry_sources');
            Route::get('/getphysician', 'get_data_bookingcontroller@getphysician')->name('getphysician');
            Route::get('/getspecialty', 'get_data_bookingcontroller@getspecialty')->name('getspecialty');
            Route::get('/getbookings', 'inpatient_bookingcontroller@getbookings')->name('getbookings');
            Route::get('/getnurse', 'get_data_bookingcontroller@getnurse')->name('getnurse');
            
            
        });
        Route::group(['namespace' => 'general'], function () {
            //nursing_station_settings
            Route::resource('nursing_station_settings', 'nursingstationcontroller');
            Route::get('nursing_station_settings/{id}/edit', 'nursingstationcontroller@edit')->name('nursing_station_settings.edit');
            Route::delete('nursing_station_settings/{id}', 'nursingstationcontroller@destroy')->name('nursing_station_settings.destroy');

            //room_settings
            Route::resource('room_settings', 'roomscontroller');
            Route::get('room_settings/{id}/edit', 'roomscontroller@edit')->name('room_settings.edit');
            Route::delete('room_settings/{id}', 'roomscontroller@destroy')->name('room_settings.destroy');

            // bed_settings
            route::resource('bed_settings', 'bedscontroller');
            route::get('bed_settings/{id}/edit', 'bedscontroller@edit')->name('bed_settings.edit');
            route::delete('bed_settings/{id}', 'bedscontroller@destroy')->name('bed_settings.destroy');
            //reasons_entry_settings
            Route::resource('reasons_entry_settings', 'reasons_e_scontroller');
            Route::get('reasons_entry_settings/{id}/edit', 'reasons_e_scontroller@edit')->name('reasons_entry_settings.edit');
            Route::delete('reasons_entry_settings/{id}', 'reasons_e_scontroller@destroy')->name('reasons_entry_settings.destroy');
            //reasons_exit_settings
            Route::resource('reasons_exit_settings', 'reasons_x_scontroller');
            Route::get('reasons_exit_settings/{id}/edit', 'reasons_x_scontroller@edit')->name('reasons_exit_settings.edit');
            Route::delete('reasons_exit_settings/{id}', 'reasons_x_scontroller@destroy')->name('reasons_exit_settings.destroy');
            //entry_sources_settings
            Route::resource('entry_sources_settings', 'reasons_s_scontroller');
            Route::get('entry_sources_settings/{id}/edit', 'reasons_s_scontroller@edit')->name('entry_sources_settings.edit');
            Route::delete('entry_sources_settings/{id}', 'reasons_s_scontroller@destroy')->name('entry_sources_settings.destroy');

            //residencies_settings
            Route::resource('residencies_settings', 'residencies_scontroller');
            Route::get('residencies_settings/{id}/edit', 'residencies_scontroller@edit')->name('residencies_settings.edit');
            Route::delete('residencies_settings/{id}', 'residencies_scontroller@destroy')->name('residencies_settings.destroy');

            //residencies_settings
            Route::resource('adm_res_pa_discharge', 'p_dis_depcontroller');
            Route::get('adm_res_pa_discharge/{id}/edit', 'p_dis_depcontroller@edit')->name('adm_res_pa_discharge.edit');
            Route::delete('adm_res_pa_discharge/{id}', 'p_dis_depcontroller@destroy')->name('adm_res_pa_discharge.destroy');
        });
    },
);

Auth::routes();
//Auth::routes(['register' => false]);