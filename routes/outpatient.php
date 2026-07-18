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
        //outpatient
        Route::group(['namespace' => 'administrative\outpatient'], function () {
            Route::resource('outpatient_programs', 'outpatientcontroller');
            Route::resource('outpatient_settings', 'outpatient_settingscontroller');
            // Route::resource('outpatient_reports', 'outpatient_reportscontroller');
            Route::resource('schedule_settings', 'schedule_settingscontroller');
            Route::resource('clinic_settings', 'clinic_settingscontroller');
            Route::get('clinic_settings/{clinic}/index', 'clinic_settingscontroller@index')->name('clinic_settings.index');

            // New route for getting medical_record
            Route::get('/get-patient', 'outpatientcontroller@getpatient')->name('get.patient');
          Route::post('/check-patient-no-shows', 'outpatientcontroller@checkPatientWarnings')->name('check.patient.no_shows');

            Route::get('medical_record/{medical_record}/edit', 'medical_recordcontroller@edit')->name('medical_record.edit');
            Route::get('medical_record/{medical_record}/show', 'medical_recordcontroller@show')->name('medical_record.show');
            Route::delete('/medical_record/{id}', [medical_recordcontroller::class, 'destroy'])->name('medical_record.destroy');
            Route::post('/get-time-slots', 'timeslotcontroller@getTimeSlots')->name('get-time-slots');
            Route::get('/get-floors/{buildingId}', 'outpatient_settingscontroller@getFloors')->name('get.Floors');
            Route::get('/get-clinics', 'outpatient_settingscontroller@getclinics')->name('get.clinics');

            Route::get('outpatient_settings/{clinic}/edit', 'outpatient_settingscontroller@edit')->name('outpatient_settings.edit');
            Route::get('outpatient_settings/{clinic}/show', 'outpatient_settingscontroller@show')->name('outpatient_settings.show');
            Route::delete('outpatient_settings/{id}', 'outpatient_settingscontroller@destroy')->name('outpatient_settings.destroy');
            Route::get('schedule_settings/{clinic}/index', 'schedule_settingscontroller@index')->name('schedule_settings.index');
            Route::get('scheduling_settings/{scheduling_settings}/edit', 'schedule_settingscontroller@edit')->name('scheduling_settings.edit');
            Route::get('schedule_settings/{clinic}/show', 'schedule_settingscontroller@show')->name('schedule_settings.show');
            Route::delete('schedule_settings/{id}', 'schedule_settingscontroller@destroy')->name('schedule_settings.destroy');
            route::get('schedule_settings/create/{id}', 'schedule_settingscontroller@create')->name('schedule_settings.create');
            Route::get('/get-schedule', 'schedule_settingscontroller@getschedule')->name('get.schedule');
            Route::get('/get-doctor-schedule', 'schedule_settingscontroller@getDoctorSchedule')->name('get.doctor.schedule');
            Route::post('/update-schedule-day', 'schedule_settingscontroller@updateScheduleDay')->name('schedule_settings.update_day');
            Route::get('/get-doctors-by-clinic', 'outpatientcontroller@getDoctorsByClinic')->name('get.doctors.by.clinic');
            Route::get('/get-clinic-services', 'outpatientcontroller@getClinicServices')->name('get-clinic-services');
            Route::get('/get_price_service', 'outpatientcontroller@get_price_service')->name('get_price_service');
            Route::post('/save-reservation', 'outpatientcontroller@store')->name('save.reservation');
            Route::post('/update-reservation', 'outpatientcontroller@update')->name('update.reservation');
            Route::get('/get-current_reservations', 'outpatientcontroller@current_reservations')->name('get.current_reservations');
            Route::get('/get-second_level_service', 'outpatientcontroller@getsecond_level_service')->name('get-second_level_service');
            Route::get('/get-last_level_service', 'outpatientcontroller@getlast_level_service')->name('get-last_level_service');
            Route::post('/save_service_data', 'outpatientservicecontroller@save_service_data')->name('save_service_data');
            Route::get('/get-service_list', 'outpatientservicecontroller@getServiceList')->name('get_service_list');
            Route::delete('/service_list/{id}', 'outpatientservicecontroller@destroy')->name('service_list.destroy');
            Route::post('/reservations/cancel', 'outpatientcontroller@cancelReservation')->name('reservations.cancel');
            Route::get('/get-future_reservations', 'outpatientfrcontroller@future_reservations')->name('get.future_reservations');
            Route::get('/doctor/nearest-appointment/{doctorId}/{clinicId}', 'schedule_settingscontroller@getNearestAppointment')->name('doctor.nearestAppointment');
            Route::get('/general_settings', 'outpatient_settingscontroller@general_settings')->name('outpatient_settings.general_settings');
            Route::get('/service_settings', 'outpatient_settingscontroller@service_settings')->name('outpatient_settings.service_settings');
                Route::post('/reservations/confirm', 'outpatientfrcontroller@confirmReservation')->name('reservations.confirm');

            Route::post('reservations/arrival_confirmed', 'outpatientcontroller@arrival_confirmed')->name('reservations.arrival_confirmed');
            Route::get('outpatient/print-barcode/{patient_id}', 'outpatientcontroller@printBarcode')->name('outpatient.print_barcode');
        });
    },
);

Auth::routes();
//Auth::routes(['register' => false]);