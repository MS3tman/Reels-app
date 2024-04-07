<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\UploadController;
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

Route::middleware('guest:sanctum')->group(function(){
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [LoginController::class, 'register']);

});

Route::middleware('auth:sanctum')->group(function(){
    Route::post('logout', [LoginController::class, 'logout']);

    Route::get('profile', [UsersController::class, 'profile']);
    Route::post('profile', [UsersController::class, 'profilePost']);
    
    Route::get('categories', [CategoriesController::class, 'listCategories']);
    Route::get('countries', [CountriesController::class, 'listCategories']);

    Route::post('upload-video', [UploadController::class, 'uploadChunks']);
    Route::get('download-video', [DownloadController::class, 'allVideo']);
    Route::get('video', [SplitVideoController::class, 'start']);
});