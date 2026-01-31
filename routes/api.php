<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\TranslationController;
use App\Http\Controllers\API\TranslationExportController;
use App\Http\Controllers\API\LocaleController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'issueToken']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix' => 'translations'], function () {
        Route::get('/', [TranslationController::class, 'index']);
        Route::post('/', [TranslationController::class, 'store']);
        Route::get('/{translation}', [TranslationController::class, 'show']);
        Route::put('/{translation}', [TranslationController::class, 'update']);
        Route::delete('/{translation}', [TranslationController::class, 'destroy']);

    });

    Route::group(['prefix' => 'tags'], function () {
        Route::get('/', [TagController::class, 'index']); //list tags with optional search and pagination
        Route::post('', [TagController::class, 'store']);
        Route::get('/{id}', [TagController::class, 'show']); //get single tag by id
        Route::put('/{id}', [TagController::class, 'update']);
        Route::delete('/{id}', [TagController::class, 'destroy']);
    });

    Route::group(['prefix' => 'locales'], function () {
        Route::get('/', [LocaleController::class, 'index']);
        Route::get('/{locale}', [LocaleController::class, 'show']);
        Route::post('/', [LocaleController::class, 'store']);
        Route::put('/{locale}', [LocaleController::class, 'update']);
        Route::delete('/{locale}', [LocaleController::class, 'destroy']);
    });
});
