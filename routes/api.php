<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Reel\DownloadController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\CountriesController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Reel\UploadController;
use App\Http\Controllers\Reel\UploderController;
use App\Http\Controllers\API\V1\Auth\AuthController as AuthAuthController;
use App\Http\Controllers\Reel\CategoryController;
use App\Http\Controllers\Reel\CountryController;

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

Route::pattern('token', '[a-zA-Z0-9]{60}');

Route::prefix('auth')->middleware('guest:sanctum')->group( function(){
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [LoginController::class, 'register']);
    Route::get('register-verify/{token}', [LoginController::class, 'verifyRegister'])->name('verify_register');
    Route::post('forget-password', [LoginController::class, 'forgetPassword']);
    Route::post('reset-password/{token}', [LoginController::class, 'resetPassword']);
});


Route::middleware('auth:sanctum')->group(function(){
    Route::post('logout', [LoginController::class, 'logout']);

    Route::group(['prefix'=>'reel'], function(){
        Route::group(['prefix'=>'create'], function(){
            Route::post('data', [UploadController::class, 'uploadReel']);
            Route::post('chunk', [UploadController::class, 'uploadChunks']);
        });

    });


    Route::group(['prefix'=>'country'], function(){
        Route::get('all', [CountryController::class, 'all']);
        Route::get('filter/{id}', [CountryController::class, 'filter']);
    });

    Route::group(['prefix'=>'category'], function(){
        Route::get('all', [CategoryController::class, 'all']);
        Route::get('filter/{id}', [CategoryController::class, 'filter']);
    });


    Route::get('profile', [UsersController::class, 'profile']);
    Route::post('profile', [UsersController::class, 'profilePost']);

    Route::get('download-video', [DownloadController::class, 'allVideo']);
    

});


