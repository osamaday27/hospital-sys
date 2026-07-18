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
        Route::group(['namespace' => 'medical\doctor'], function () {
            Route::resource('doctor_settings', 'doctor_settingscontroller');
            Route::resource('doctor_programs', 'doctor_programscontroller');
            Route::resource('doctor_booking', 'doctor_bookingcontroller');
            Route::resource('outpatient_clinics', 'outpatient_clinicscontroller');
            Route::resource('inpatient_doctor', 'inpatient_bookingcontroller');
            Route::get('/getbookingsdoctor', 'inpatient_bookingcontroller@getbookings')->name('getbookings');
            Route::get('start_session_in/{id}/start', 'inpatient_bookingcontroller@start')->name('start_session_in.start');
            Route::post('/ophthalmology_examination/end-session/{id}', 'ophthalmology_examinationcontroller@endSession')->name('ophthalmology_examination.end_session');
            Route::post('/ophthalmology/end-session/{id}', 'ophthalmologycontroller@endSession')->name('ophthalmology.end_session');
            Route::get('start_session_out_ophthalmology/{id}/open', 'ophthalmologycontroller@open')->name('start_session_out_ophthalmology.open');
            Route::get('/setting_medical_sheets', 'doctor_settingscontroller@setting_medical_sheets')->name('setting_medical_sheets');
            Route::get('/medical/templates/{id}/builder', 'doctor_settingscontroller@builder_medical_sheets')->name('medical.templates.builder');

            Route::get('/get-copc', 'outpatient_clinicscontroller@current_reservations')->name('get.copc');
            Route::get('start_session_out/{id}/start', 'outpatient_clinicscontroller@start')->name('start_session_out.start');
            Route::get('/vital-signs/{patient_id}', 'vitalsigncontroller@index')->name('vital-signs.index');
            Route::post('/vital-signs', 'vitalsigncontroller@store')->name('vital-signs.store');

            Route::resource('setting_complaining', 'setting_complainingcontroller');
            Route::get('setting_complaining/{id}/edit', 'setting_complainingcontroller@edit')->name('setting_complaining.edit');
            Route::delete('setting_complaining/{id}', 'setting_complainingcontroller@destroy')->name('setting_complaining.destroy');
            Route::get('/autocomplete/complaint-categories', 'setting_complainingcontroller@autocomplete')->name('autocomplete.complaint_categories');
            // routes/web.php
            Route::post('/save-complaint', 'complaintcontroller@saveComplaint')->name('save.complaint');
            Route::delete('/complaints/{id}', 'complaintcontroller@destroy')->name('delete.complaint');
            Route::get('/complaint/{patient_id}', 'complaintcontroller@index')->name('complaint.index');
            Route::get('/complaints/{id}', 'complaintcontroller@show')->name('complaint.show');
            Route::put('/complaints/{id}', 'complaintcontroller@update')->name('complaint.update');

            Route::post('/save-medical_history', 'complaintcontroller@saveComplaint')->name('save.medical_history');
            Route::post('/delete-medical_history', 'complaintcontroller@destroy')->name('delete.medical_history');

            Route::resource('setting_examination', 'setting_examinationcontroller');
            Route::get('setting_examination/{id}/edit', 'setting_examinationcontroller@edit')->name('setting_examination.edit');
            Route::delete('setting_examination/{id}', 'setting_examinationcontroller@destroy')->name('setting_examination.destroy');

            Route::resource('setting_allergies', 'setting_allergiescontroller');
            Route::get('setting_allergies/{id}/edit', 'setting_allergiescontroller@edit')->name('setting_allergies.edit');
            Route::delete('setting_allergies/{id}', 'setting_allergiescontroller@destroy')->name('setting_allergies.destroy');

            Route::resource('setting_medical_history', 'setting_diseasescontroller');
            Route::get('setting_medical_history/{id}/edit', 'setting_diseasescontroller@edit')->name('setting_medical_history.edit');
            Route::delete('setting_medical_history/{id}', 'setting_diseasescontroller@destroy')->name('setting_medical_history.destroy');
            Route::resource('get_lab', 'get_labcontroller');
            Route::resource('get_rad', 'get_radcontroller');

            Route::post('save_doctor_order', 'doctor_ordercontroller@save_doctor_order')->name('save_doctor_order');
            Route::post('save_general_examination', 'generalexaminationcontroller@saveGeneralExamination')->name('save_general_examination');
            Route::post('/pain-assessment/store', 'mdpainassessmentcontroller@store')->name('pain_assessment.store');
            Route::post('/save_ophthalmology_assessment', 'ophthalmologyassessmentcontroller@store');
            Route::get('/get_sheet_history/{id_patient}', 'ophthalmologyassessmentcontroller@get_sheet_history')->name('get.sheet_history');
            Route::delete('/sheet-history/{id}', 'ophthalmologyassessmentcontroller@delete_sheet_history')->name('delete.sheet_history');

            Route::post('/save_follow_ophthalmology_assessment', 'ophthalmologyassessmentfollowcontroller@store');
            Route::get('/get_sheet_history/{id_patient}', 'ophthalmologyassessmentcontroller@get_sheet_history')->name('get.sheet_history');
            Route::delete('/sheet-history/{id}', 'ophthalmologyassessmentcontroller@delete_sheet_history')->name('delete.sheet_history');

            Route::post('/general_examinations/store', 'mdgeneralexaminationcontroller@store')->name('general_examinations.store');
            Route::get('/general_examinations/{patient_id}', 'mdgeneralexaminationcontroller@index')->name('general_examinations.index');

            Route::post('/surgery_requests/store', 'surgeryrequestscontroller@store')->name('surgery_requests.store');
            Route::get('/surgery_requests/{patient_id}', 'surgeryrequestscontroller@index')->name('surgery_requests.index');

            Route::post('/request_medical_examination/store', 'request_medical_examinationcontroller@store')->name('request_medical_examination.store');
            Route::get('/request_medical_examination/{patient_id}', 'request_medical_examinationcontroller@index')->name('request_medical_examination.index');

            Route::get('/get-services', 'request_medical_examinationcontroller@getServices')->name('get.services');
            Route::get('/get-specialty', 'request_medical_examinationcontroller@getspecialty')->name('get.specialty');

            Route::post('/patienteducation/store', 'patienteducationcontroller@store')->name('patienteducation.store');
            // Route::get('/patienteducation/{patient_id}', 'patienteducationcontroller@index')->name('patienteducation.index');
            Route::resource('ophthalmology', 'ophthalmologycontroller');
            Route::get('start_session_out_ophthalmology/{id}/start', 'ophthalmologycontroller@start')->name('start_session_out_ophthalmology.start');
            Route::get('/get-ophthalmology_out', 'ophthalmologycontroller@current_reservations')->name('get.ophthalmology_out');
            Route::resource('ophthalmology_examination', 'ophthalmology_examinationcontroller');
            Route::get('start_session_out_ophthalmology_examination/{id}/start', 'ophthalmology_examinationcontroller@start')->name('start_session_out_ophthalmology_examination.start');
            Route::get('/get-ophthalmology_examination_out', 'ophthalmology_examinationcontroller@current_reservations')->name('get.ophthalmology_examination_out');

            Route::get('/load-overview', 'viewfilecontroller@loadOverview')->name('load.overview');
            Route::get('/load-complaint', 'viewfilecontroller@loadComplaint')->name('load.complaint');
            Route::get('/load-medical-history', 'viewfilecontroller@loadmedicalHistory')->name('load.medical_history');

            Route::get('/autocomplete/complaint-diagnosis', 'setting_diseasescontroller@autocomplete')->name('autocomplete.diagnosis');

            Route::get('/disease-history', 'ophthalmologydiseasehistorycontroller@index')->name('ophthalmology-history.index');
            Route::post('/disease-history', 'ophthalmologydiseasehistorycontroller@store')->name('ophthalmology-history.store');
            Route::delete('/disease-history/{id}', 'ophthalmologydiseasehistorycontroller@destroy')->name('ophthalmology-history.destroy');

            Route::resource('setting_repport', 'setting_repportcontroller');

            ///dentistry
            Route::resource('dentistry', 'dentistrycontroller');
            Route::get('start_session_out_dentistry/{id}/start', 'dentistrycontroller@start')->name('start_session_out_dentistry.start');
            Route::get('/get-dentistry_out', 'dentistrycontroller@current_reservations')->name('get.dentistry_out');

            Route::post('/ajax/save-fundus-image', 'FundusController@store')->name('ajax.saveFundusImage');
        });
    },
);

Auth::routes();