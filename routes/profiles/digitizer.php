<?php

use App\Http\Controllers\Birthday;
use App\Http\Controllers\ConsecutivoController;
use App\Http\Controllers\CorrectionsController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ErroresController;
use App\Http\Controllers\OtherTimesController;
use App\Http\Controllers\ProductivityController;
use Illuminate\Support\Facades\Route;

Route::prefix('dig')->group(function () {
    Route::get('/productivity', [ProductivityController::class, 'index'])->name('productivity.index');
    Route::get('/productivity-month', [ProductivityController::class, 'productivityMonth'])->name('productivity.month');
    Route::get('/productivity-day', [ProductivityController::class, 'productivityDay']);
    Route::post('/productivity/store', [ProductivityController::class, 'store'])->name('productivity.store');
    Route::post('/corrections/store', [CorrectionsController::class, 'store']);    
    Route::post('/likes-birthday', [Birthday::class, 'likes'])->name('likes-birthday');
    Route::post('/other-times', [OtherTimesController::class, 'store'])->name('dig.request.othertimes');
});

Route::get('dig/codeInjector/{id}', function () {
    return view("dig.codeInjector");
})->name('codeInjector');

Route::controller(ConsecutivoController::class)->group(function (){
    Route::get('dig/consecutivos', 'index')->name('consecutivos.index');
    Route::get('dig/consecutivos/show', 'show')->name('consecutivos.show');
    Route::get('dig/consecutivos/filter-by-user', 'filterByUser')->name('consecutivos.filterByUser');
    Route::post('dig/consecutivos/store', 'store')->name('consecutivos.store');
});

Route::post('/chatbot', [ChatbotController::class , 'getResponse']);

//Route::post('/ai/generate', [AiApiController::class, 'generate']);
Route::get('/mis-errores/{entorno}', [ErroresController::class, 'show'])->name('errors.show');
Route::patch('error-fields/{errorRecordField}/resolve', [ErroresController::class, 'resolveField'])->name('error.field.resolve');


Route::get('/obtener-entorno', function (Request $request) {
    $json = Storage::disk('public')->get('tiempo_dia.json');
    return response()->json(json_decode($json, true));
});