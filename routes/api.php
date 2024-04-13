<?php

use App\Http\Controllers\API\V1\Auth\AuthController as AuthAuthController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoriesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Reel\UploadController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\CountriesController;
use App\Http\Controllers\SplitVideoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('guest:sanctum')->group( function(){
    Route::group(['prefix'=>'auth'], function(){
        Route::post('verify-phone-register', [AuthController::class, 'verifyPhoneNumberForRegister']);
        Route::post('verify-otp-register', [AuthController::class, 'verifyOtpWithoutToken']);
        Route::post('register', [AuthController::class, 'register']);

        Route::post('login-phone', [AuthController::class, 'loginByPhone']);
        Route::post('verify-phone-login', [AuthController::class, 'verifyPhoneNumber']);
        Route::post('verify-otp-login', [AuthController::class, 'verifyOtpWithToken']);
        
        Route::post('verify-phone-password', [AuthController::class, 'verifyPhoneNumber']);
        Route::post('verify-otp-password', [AuthController::class, 'verifyOtpWithoutToken']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
    });
});


Route::middleware('auth:sanctum')->group(function(){
    Route::post('logout', [AuthController::class, 'logout']);

    Route::group(['prefix'=>'reel'], function(){
        Route::group(['prefix'=>'create'], function(){
            Route::post('data', [UploadController::class, 'uploadReel']);
            Route::post('chunk', [UploadController::class, 'uploadChunks']);
        });

        

    });


    Route::get('profile', [UsersController::class, 'profile']);
    Route::post('profile', [UsersController::class, 'profilePost']);
    
    Route::get('categories', [CategoriesController::class, 'listCategories']);
    Route::get('countries', [CountriesController::class, 'listCategories']);

    Route::post('upload-video', [UploadController::class, 'uploadChunks']);
    Route::get('download-video', [DownloadController::class, 'allVideo']);
    Route::get('video', [SplitVideoController::class, 'start']);
});


