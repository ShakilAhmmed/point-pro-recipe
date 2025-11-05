<?php

use App\Http\Controllers\Api\V1\AuthenticationController;
use App\Http\Controllers\Api\V1\RegistrationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {

    Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', [AuthenticationController::class, 'login']);
        Route::post('/register', [RegistrationController::class, 'store']);
        Route::post('/refresh', [AuthenticationController::class, 'refreshToken']);
        Route::post('/logout', [AuthenticationController::class, 'logout'])->middleware('auth:api');
    });

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });
});
