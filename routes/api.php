<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\FormatController;
use App\Http\Controllers\Api\V1\PackageController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\ReceptionController;
use App\Http\Controllers\Api\V1\ColegiosController;
use App\Http\Controllers\Api\V1\ProductivitySdsController;
use App\Http\Controllers\Api\V2\UserController as V2UserController;

Route::post('login', [App\Http\Controllers\Api\LoginController::class, 'login']);
Route::post('v1/validate-license', [App\Http\Controllers\Api\V1\LicenseController::class, 'validateLicense'])->name('validate.license');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('v1/users', UserController::class)->only(['index', 'show', 'update']);
    Route::apiResource('v1/profiles', ProfileController::class);
    Route::apiResource('v1/receptions', ReceptionController::class)->only(['index', 'show', 'update']);
    Route::apiResource('v1/formats', FormatController::class);
    Route::apiResource('v1/packages', PackageController::class);

});

Route::post('/colegios', [ColegiosController::class, 'guardarColegio']);
Route::get('v2/users', [V2UserController::class, 'index']);
Route::post('v1/store-productivity-sds', [ProductivitySdsController::class, 'storeProductivitySds'])->name('store.productivity.sds');