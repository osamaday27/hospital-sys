<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\assets\assetscontroller;
use App\Http\Controllers\assets\disposalcontroller;
use App\Http\Controllers\assets\maintenancecontroller;
use App\Http\Controllers\assets\depreciationcontroller;
use App\Http\Controllers\assets\convert_to_assetscontroller;
use App\Http\Controllers\assets\asset_conversionscontroller;
use App\Http\Controllers\assets\AssetAssignmentsController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Auth::routes(['verify' => true]);

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'auth', 'verified'],
    ],
    function () {
        //call_center
        Route::group(['namespace' => 'assets'], function () {
            Route::get('assets_programs', 'assetscontroller@programs')->name('assets_programs');
            Route::get('assets_settings', 'assetscontroller@settings')->name('settings_programs');
            Route::get('assets_reports', 'assetscontroller@reports')->name('reports_programs');


            // ========== SETTINGS (CRUD + AJAX) ==========
            Route::prefix('assets/settings')->name('assets.settings.')->group(function () {
                // Categories
                Route::get('categories', 'AssetCategoriesController@index')->name('categories.index');
                Route::get('categories/create', 'AssetCategoriesController@create')->name('categories.create');
                Route::post('categories', 'AssetCategoriesController@store')->name('categories.store');
                Route::get('categories/{id}', 'AssetCategoriesController@show')->name('categories.show');
                Route::get('categories/{id}/edit', 'AssetCategoriesController@edit')->name('categories.edit');
                Route::put('categories/{id}', 'AssetCategoriesController@update')->name('categories.update');
                Route::delete('categories/{id}', 'AssetCategoriesController@destroy')->name('categories.destroy');


                // AJAX endpoints
                Route::get('ajax/categories', 'AssetCategoriesController@ajaxIndex')->name('categories.ajax');
                Route::get('ajax/categories/select2', 'AssetCategoriesController@ajaxSelect2')->name('categories.select2');


                // Brands
                Route::get('brands', 'AssetBrandsController@index')->name('brands.index');
                Route::get('brands/create', 'AssetBrandsController@create')->name('brands.create');
                Route::post('brands', 'AssetBrandsController@store')->name('brands.store');
                Route::get('brands/{id}', 'AssetBrandsController@show')->name('brands.show');
                Route::get('brands/{id}/edit', 'AssetBrandsController@edit')->name('brands.edit');
                Route::put('brands/{id}', 'AssetBrandsController@update')->name('brands.update');
                Route::delete('brands/{id}', 'AssetBrandsController@destroy')->name('brands.destroy');
                Route::get('ajax/brands', 'AssetBrandsController@ajaxIndex')->name('brands.ajax');
                Route::get('ajax/brands/select2', 'AssetBrandsController@ajaxSelect2')->name('brands.select2');


                // Models
                Route::get('models', 'AssetModelsController@index')->name('models.index');
                Route::get('models/create', 'AssetModelsController@create')->name('models.create');
                Route::post('models', 'AssetModelsController@store')->name('models.store');
                Route::get('models/{id}', 'AssetModelsController@show')->name('models.show');
                Route::get('models/{id}/edit', 'AssetModelsController@edit')->name('models.edit');
                Route::put('models/{id}', 'AssetModelsController@update')->name('models.update');
                Route::delete('models/{id}', 'AssetModelsController@destroy')->name('models.destroy');
                Route::get('ajax/models', 'AssetModelsController@ajaxIndex')->name('models.ajax');
                Route::get('ajax/models/select2', 'AssetModelsController@ajaxSelect2')->name('models.select2');


                // Locations
                Route::get('locations', 'AssetLocationsController@index')->name('locations.index');
                Route::get('locations/create', 'AssetLocationsController@create')->name('locations.create');
                Route::post('locations', 'AssetLocationsController@store')->name('locations.store');
                Route::get('locations/{id}', 'AssetLocationsController@show')->name('locations.show');
                Route::get('locations/{id}/edit', 'AssetLocationsController@edit')->name('locations.edit');
                Route::put('locations/{id}', 'AssetLocationsController@update')->name('locations.update');
                Route::delete('locations/{id}', 'AssetLocationsController@destroy')->name('locations.destroy');
                Route::get('ajax/locations', 'AssetLocationsController@ajaxIndex')->name('locations.ajax');
                Route::get('ajax/locations/tree', 'AssetLocationsController@ajaxTree')->name('locations.tree');
                Route::get('ajax/locations/select2', 'AssetLocationsController@ajaxSelect2')->name('locations.select2');


                // General Settings (single record)
                Route::get('general', 'AssetSettingController@edit')->name('general.edit');
                Route::put('general', 'AssetSettingController@update')->name('general.update');
            });
            // assets/programs
            Route::group(['prefix' => 'assets/programs', 'as' => 'assets.programs.'], function () {
                // ITEMS
                Route::get('items/ajax', 'AssetItemsController@ajaxIndex')->name('items.ajax');
                Route::get('items/{id}/print-card', 'AssetItemsController@printCard')->name('items.print');
                Route::resource('items', 'AssetItemsController');

                // DOCUMENTS
                Route::get('documents/ajax', 'AssetDocumentsController@ajaxIndex')->name('documents.ajax');
                Route::resource('documents', 'AssetDocumentsController');
            });

            //assignments

            Route::prefix('assets/programs/assignments')->name('assets.programs.assignments.')->group(function () {
                Route::get('/',               [AssetAssignmentsController::class, 'index'])->name('index');
                Route::get('/ajax',           [AssetAssignmentsController::class, 'ajaxIndex'])->name('ajax');

                Route::get('/create',         [AssetAssignmentsController::class, 'create'])->name('create');
                Route::post('/',              [AssetAssignmentsController::class, 'store'])->name('store');

                Route::get('/{id}/edit',      [AssetAssignmentsController::class, 'edit'])->name('edit');
                Route::put('/{id}',           [AssetAssignmentsController::class, 'update'])->name('update');

                Route::patch('/{id}/return',  [AssetAssignmentsController::class, 'return'])->name('return');
                Route::delete('/{id}',        [AssetAssignmentsController::class, 'destroy'])->name('destroy');

                // Select2 sources (جديدة)
                Route::get('/select2/employees', [AssetAssignmentsController::class, 'ajaxEmployeesSelect2'])->name('select2.employees');
                Route::get('/select2/sections',  [AssetAssignmentsController::class, 'ajaxSectionsSelect2'])->name('select2.sections');
                Route::get('/select2/stores',    [AssetAssignmentsController::class, 'ajaxStoresSelect2'])->name('select2.stores');
                Route::get('/select2/suppliers', [AssetAssignmentsController::class, 'ajaxSuppliersSelect2'])->name('select2.suppliers');
            });

            //maintenance
            Route::prefix('assets/programs/maintenance')->name('assets.programs.maintenance.')->group(function () {
                Route::get('/',                [maintenancecontroller::class, 'index'])->name('index');
                Route::get('/create',          [maintenancecontroller::class, 'create'])->name('create');
                Route::post('/',               [maintenancecontroller::class, 'store'])->name('store');
                Route::get('/{id}',            [maintenancecontroller::class, 'show'])->name('show');
                Route::get('/{id}/edit',       [maintenancecontroller::class, 'edit'])->name('edit');
                Route::put('/{id}',            [maintenancecontroller::class, 'update'])->name('update');
                Route::delete('/{id}',         [maintenancecontroller::class, 'destroy'])->name('destroy');



                // AJAX:
                Route::get('/ajax/assets',     [maintenancecontroller::class, 'ajaxAssets'])->name('ajax.assets');
                Route::get('/ajax/vendors',    [maintenancecontroller::class, 'ajaxVendors'])->name('ajax.vendors');
            });

            // depreciation
            Route::prefix('assets/programs/depreciation')->name('assets.programs.depreciation.')->group(function () {
                Route::get('/',                [depreciationcontroller::class, 'index'])->name('index');
                Route::get('/create',          [depreciationcontroller::class, 'create'])->name('create');
                Route::post('/preview',        [depreciationcontroller::class, 'preview'])->name('preview');
                Route::post('/',               [depreciationcontroller::class, 'store'])->name('store');
                Route::get('/{id}',            [depreciationcontroller::class, 'show'])->name('show');
                Route::post('/{id}/post',      [depreciationcontroller::class, 'post'])->name('post');
                Route::post('/{id}/unpost',    [depreciationcontroller::class, 'unpost'])->name('unpost');
                Route::get('/{id}/export/csv', [depreciationcontroller::class, 'exportCsv'])->name('export.csv');
                Route::get('/{id}/export/print', [depreciationcontroller::class, 'exportPrint'])->name('export.print');
            });


            // audits
            Route::prefix('assets/programs/audits')->name('assets.programs.audits.')->group(function () {
                Route::get('/',                [\App\Http\Controllers\assets\auditscontroller::class, 'index'])->name('index');

                // Ajax
                Route::get('/ajax',            [\App\Http\Controllers\assets\auditscontroller::class, 'ajaxAudits'])->name('ajax');
                Route::get('/{id}/lines',      [\App\Http\Controllers\assets\auditscontroller::class, 'ajaxLines'])->name('lines');

                // Actions
                Route::post('/start',          [\App\Http\Controllers\assets\auditscontroller::class, 'start'])->name('start');
                Route::post('/scan',           [\App\Http\Controllers\assets\auditscontroller::class, 'scan'])->name('scan');
                Route::post('/{id}/apply',     [\App\Http\Controllers\assets\auditscontroller::class, 'applySelection'])->name('apply');
                Route::post('/{id}/reconcile', [\App\Http\Controllers\assets\auditscontroller::class, 'reconcile'])->name('reconcile');
                Route::post('/{id}/void',      [\App\Http\Controllers\assets\auditscontroller::class, 'void'])->name('void');
                Route::get('/{id}/export',     [\App\Http\Controllers\assets\auditscontroller::class, 'exportCsv'])->name('export');

                // Inline update (جديد)
                Route::post('/{auditId}/lines/{lineId}/update', [\App\Http\Controllers\assets\auditscontroller::class, 'updateLine'])->name('line.update');
                Route::post('/{auditId}/lines/{lineId}/draft', [\App\Http\Controllers\assets\auditscontroller::class, 'saveDraftAsset'])
                    ->name('line.draft');
                Route::get('/{id}/print', [\App\Http\Controllers\assets\auditscontroller::class, 'print'])->name('print');
            });


            //disposal

            Route::prefix('assets/programs/disposal')->name('assets.programs.disposal.')->group(function () {
                Route::get('/', [disposalcontroller::class, 'index'])->name('index');
                Route::get('/ajax', [disposalcontroller::class, 'ajaxIndex'])->name('ajax.index');
                Route::get('/ajax/assets', [disposalcontroller::class, 'ajaxAssets'])->name('ajax.assets');
                Route::get('/ajax/users', [disposalcontroller::class, 'ajaxUsers'])->name('ajax.users');

                Route::get('/create', [disposalcontroller::class, 'create'])->name('create');
                Route::post('/store', [disposalcontroller::class, 'store'])->name('store');

                Route::get('/{id}', [disposalcontroller::class, 'show'])->name('show');
                Route::post('/{id}/submit', [disposalcontroller::class, 'submit'])->name('submit');
                Route::post('/{id}/approve', [disposalcontroller::class, 'approve'])->name('approve');

                Route::get('/{id}/execute', [disposalcontroller::class, 'executeForm'])->name('execute.form');
                Route::post('/{id}/execute', [disposalcontroller::class, 'execute'])->name('execute');

                Route::post('/{id}/cancel', [disposalcontroller::class, 'cancel'])->name('cancel');

                // Attachments
                Route::post('/{id}/attachments', [disposalcontroller::class, 'addAttachment'])->name('attachment.add');
                Route::post('/{id}/attachments/{attId}/delete', [disposalcontroller::class, 'deleteAttachment'])->name('attachment.delete');

                // Print
                Route::get('/{id}/print/request', [disposalcontroller::class, 'printRequest'])->name('print.request');
                Route::get('/{id}/print/committee', [disposalcontroller::class, 'printCommittee'])->name('print.committee');
                Route::get('/{id}/print/execution', [disposalcontroller::class, 'printExecution'])->name('print.execution');
            });

            //convert_to_assets
            Route::prefix('assets/programs/convert_to_assets')
                ->name('assets.programs.convert_to_assets.')
                ->group(function () {
                    Route::get('/', [convert_to_assetscontroller::class, 'index'])->name('index');
                    Route::get('/{doc}', [convert_to_assetscontroller::class, 'show'])->name('show');
                    Route::post('/{doc}/convert', [convert_to_assetscontroller::class, 'convert'])->name('convert');
                });

            Route::prefix('assets/programs/asset_conversions')
                ->name('assets.programs.asset_conversions.')
                ->group(function () {
                    Route::get('/', [asset_conversionscontroller::class, 'index'])->name('index');           // DataTables
                    Route::get('/{id}', [asset_conversionscontroller::class, 'show'])->name('show');         // عرض
                    Route::post('/{id}/cancel', [asset_conversionscontroller::class, 'cancel'])->name('cancel'); // إلغاء
                    // (اختياري) تعديل:
                    Route::post('/{id}/update', [asset_conversionscontroller::class, 'update'])->name('update');
                });

                //reports
                Route::resource('master_movement_report', 'master_movement_reportcontroller');
                Route::resource('depreciation_report', 'depreciation_reportcontroller');
                Route::resource('maintenance_report', 'maintenance_reportcontroller');
                Route::resource('audit_report', 'audit_reportcontroller');
                Route::resource('documents_report', 'documents_reportcontroller');
                Route::resource('disposal_lifecycle_report', 'disposal_lifecycle_reportcontroller');





        });
    },
);

Auth::routes();
//Auth::routes(['register' => false]);
