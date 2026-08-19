<?php

use Illuminate\Support\Facades\Route;

//Middlewares
use App\Http\Middleware\AdminGesiMiddleware;

//Administradores
use App\Http\Controllers\ActsController;
use App\Http\Controllers\CorrectionsController;
use App\Http\Controllers\FormatController;
use App\Http\Controllers\PackagesController;
use App\Http\Controllers\ProductivityController;
use App\Http\Controllers\ReceptionsController;
use App\Http\Controllers\ReceptionSiscoController;
use App\Http\Controllers\TraceabilityController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ConsecutivoPermissionController;
use App\Http\Controllers\ErrorImportController;



Route::middleware([AdminGesiMiddleware::class])->group(function () {
    //Recepciones    
    Route::controller(ReceptionsController::class)->group(function () {
        Route::get('/receptions/dashboard',          'receptionsDashboard')->name('receptions.dashboard');
        Route::post('receptions/update/{reception}', 'update')->name('receptions.update');
        Route::get('receptions/{environment}',       'index')->name('receptions.index');
        Route::get('receptions/list/{environment}',  'list')->name('receptions.list');
        Route::delete('receptions/destroy',          'destroy')->name('receptions.destroy');
        Route::post('receptions/return-to-fix',      'returnToFix')->name('receptions.returnToFix');
        Route::put('receptions/store',               'store')->name('receptions.store');
        Route::post('receptions/receive',            'receive')->name('reception.receive');
        Route::post('receptions/sisco',              'receiveSisco')->name('reception.receive.sisco');
    });

    Route::controller(ReceptionSiscoController::class)->group(function () {
        Route::post('receptions/sisco/update/{reception}', 'update')->name('receptions.sisco.update');
    });

    Route::controller(TraceabilityController::class)->group(function () {
        Route::get('traceability',         'administrators')->name('adm.traceability');
    });

    Route::controller(ProductivityController::class)->group(function () {
        Route::get('productivity/edit/{productivity}', 'edit')->name('productivity.edit');
        Route::post('productivity/dashboard',          'dashboardAdmin')->name('adm.dashboard.productivity');
        Route::delete('productivity/{productivity}',   'delete')->name('productivity.delete');
        Route::post('productivity/{id}',               'restore')->name('productivity.restore');
    });

    //Bases
    Route::resource('formats', FormatController::class)->except(['destroy']);

    Route::post('adm/formats', [FormatController::class, 'store'])
        ->name('formats.add');

    Route::post('adm/formats/associate', [FormatController::class, 'formatsAssociateSon'])
        ->name('formats.associate.son');
    
    Route::patch('adm/formats/{format}/activate', [FormatController::class, 'activate'])
    ->name('formats.activate');

    Route::patch('adm/formats/{format}/deactivate', [FormatController::class, 'deactivate'])
        ->name('formats.deactivate');

    Route::put('adm/formats/{format}', [FormatController::class, 'update'])
        ->name('formats.update');

    //Paquetes
    Route::controller(PackagesController::class)->group(function () {
        Route::get('packages/{environment}',   'index')->name('packages.index');
        Route::put('adm/packages/{package}',   'return')->name('package.return');
        Route::put('adm/packageDig/{package}', 'returnDigitizer')->name('package.returnDigitizer');
    });

    //Actas
    Route::controller(ActsController::class)->group(function () {
        Route::get('acts',                 'index')->name('adm.acts');
        Route::post('/adm/acts/users',     'environmentUsers');
        Route::post('/adm/acts/formats',   'getFormats');
        Route::post('adm/acts/receptions', 'generateReceptionsAct')->name('pdf.receptions');
        Route::post('acts/download',       'actsDownload')->name('acts.download');
    });

    //Usuarios
    Route::controller(UsersController::class)->group(function () {
        Route::get('users',                   'index')->name('users.index');
        Route::post('adm/users/getDigitizer', 'getDigitizer');
    });

    //Correcciones
    Route::controller(CorrectionsController::class)->group(function () {
        Route::get('adm/corrections',                'index')->name('adm.corrections');
        Route::post('adm/corrections/',              'show')->name('corrections.show');
        Route::put('adm/corrections/{correction}',   'update');
        Route::delete('adm/corrections/delete/{correction}', 'destroy');
    });

    //Importación de errores de digitación (Excel)
    Route::controller(ErrorImportController::class)->group(function () {
        Route::get('adm/error-imports',          'index')->name('adm.error-imports.index');
        Route::patch('adm/error-imports/{errorImport}', 'updateMeta')->name('adm.error-imports.update');
        Route::delete('adm/error-imports/{errorImport}', 'destroy')->name('adm.error-imports.destroy');
        Route::get('adm/error-imports/test',     'testView')->name('adm.error-imports.test');
        Route::post('adm/error-imports/preview', 'preview')->name('adm.error-imports.preview');
        Route::post('adm/error-imports/commit',  'commit')->name('adm.error-imports.commit');
    });

    //Permisos de consecutivos del mes anterior
    Route::controller(ConsecutivoPermissionController::class)->group(function () {
        Route::patch('adm/consecutivo-permisos/{consecutivoPermission}/aprobar',  'approve')->name('consecutivo.permission.approve');
        Route::patch('adm/consecutivo-permisos/{consecutivoPermission}/rechazar', 'reject')->name('consecutivo.permission.reject');
    });
});
