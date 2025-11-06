<?php

use App\Http\Controllers\Api\V1\AuthenticationController;
use App\Http\Controllers\Api\V1\PasswordManageController;
use App\Http\Controllers\Api\V1\RecipeController;
use App\Http\Controllers\Api\V1\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {

    Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', [AuthenticationController::class, 'login']);
        Route::post('/register', [RegistrationController::class, 'store']);
        Route::post('/refresh', [AuthenticationController::class, 'refreshToken']);
        Route::post('/forgot-password', [PasswordManageController::class, 'sendResetLink'])
            ->middleware('throttle:5,1');
        Route::post('/reset-password', [PasswordManageController::class, 'reset'])
            ->middleware('throttle:10,1');
        Route::post('/logout', [AuthenticationController::class, 'logout'])
            ->middleware('auth:api');
    });

    Route::group(['middleware' => 'auth:api'], function () {
        Route::apiResource('/recipes', RecipeController::class);
    });
});
