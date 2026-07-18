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
        //surgical
        Route::group(['namespace' => 'medical\nursing'], function () {
            Route::resource('nursing_settings', 'nursing_programs');
            Route::resource('nursing_programs', 'nursing_programscontroller');
            Route::resource('nursing_booking', 'nursing_bookingcontroller');
            Route::resource('nursing_outpatient_clinics', 'outpatient_clinicscontroller');
            Route::get('nursing_outpatient_clinics/next/{id}','outpatient_clinicscontroller@getNextPatient')->name('nursing_outpatient_clinics.next');

            Route::post('/nursing/end-session/{id}', 'outpatient_clinicscontroller@endSession')->name('nursing.end_session');
            Route::get('/get-nopc', 'outpatient_clinicscontroller@current_reservations')->name('get.nopc');

            Route::get('n_start_session_out/{id}/start', 'outpatient_clinicscontroller@start')->name('n_start_session_out.start');
            Route::get('n_start_session_in/{id}/start', 'inpatient_bookingcontroller@start')->name('n_start_session_in.start');
            Route::resource('nursing_inpatient', 'inpatient_bookingcontroller');
            Route::get('/getbookingsnursing', 'inpatient_bookingcontroller@getbookings')->name('getbookingnursing');
            Route::post('/n_save_service_data', 'outpatientservicecontroller@save_service_data')->name('n_save_service_data');
            Route::get('/n_get-service_list', 'outpatientservicecontroller@getServiceList')->name('n_get_service_list');
            Route::delete('/n_service_list/{id}', 'outpatientservicecontroller@destroy')->name('n_service_list.destroy');
            Route::get('/n_get-second_level_service', 'outpatientservicecontroller@getsecond_level_service')->name('n_get-second_level_service');
            Route::get('/n_get-last_level_service', 'outpatientservicecontroller@getlast_level_service')->name('n_get-last_level_service');
            Route::get('/n_get_price_service', 'outpatientservicecontroller@get_price_service')->name('n_get_price_service');
            Route::get('/pain-assessment', 'medicalsheetcontroller@painAssessment')->name('medicalsheet.pain_assessment');
            Route::get('/initial_assessmentmedicalsheet', 'medicalsheetcontroller@initialAssessment')->name('medicalsheet.initial_assessmentmedicalsheet');
            Route::get('/follow_up', 'medicalsheetcontroller@follow_up')->name('medicalsheet.follow_up');
            Route::get('/lab-tests', 'medicalsheetcontroller@labTests')->name('medicalsheet.lab_tests');
            Route::get('/radiology', 'medicalsheetcontroller@radiology')->name('medicalsheet.radiology');

            Route::resource('reassessmentform', 'reassessmentformcontroller');

            Route::get('nursing_add_services/{reservation_id}', 'outpatientservicecontroller@add_services')->name('nursing.add_services');
            Route::get('nursing_add_supplies/{reservation_id}', 'outpatientservicecontroller@add_supplies')->name('nursing.add_supplies');
        });
    },
);

Auth::routes();