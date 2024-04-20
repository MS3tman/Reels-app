<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Reel\DownloadController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\CountriesController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\API\V1\Auth\AuthController as AuthAuthController;
use App\Http\Controllers\Reel\CategoryController;
use App\Http\Controllers\Reel\CountryController;
use App\Http\Controllers\Reel\ReelChunkController;
use App\Http\Controllers\Reel\ReelsController;

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
Route::pattern('id', '[0-9]');

Route::prefix('auth')->middleware('guest:sanctum')->group( function(){
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [LoginController::class, 'register']);
    Route::get('register-verify/{token}', [LoginController::class, 'verifyRegister'])->name('verify_register');
    Route::post('forget-password', [LoginController::class, 'forgetPassword']);
    Route::post('reset-password/{token}', [LoginController::class, 'resetPassword']);
    
});

Route::group(['prefix'=>'country'], function(){
    Route::get('all', [CountryController::class, 'all']);
    Route::get('filter/{id}', [CountryController::class, 'filter']);
});

Route::middleware('auth:sanctum')->group(function(){
    Route::post('logout', [LoginController::class, 'logout']);

    Route::group(['prefix'=>'reel'], function(){
        Route::post('create/chunk', [ReelChunkController::class, 'uploadChunks']);
        Route::get('list', [ReelsController::class, 'reelsList']);
        Route::get('{id}', [ReelsController::class, 'reelsById']);
        Route::get('user/list', [ReelsController::class, 'reelsListForUser']);
        Route::get('user/{id}', [ReelsController::class, 'reelsByIdForUser']);
        Route::post('store', [ReelsController::class, 'reelsStore']);
        Route::put('update/{id}', [ReelsController::class, 'reelsUpdate']);
        Route::put('update/video/{id}', [ReelsController::class, 'reelsVideoUpdate']);
        Route::put('update/views/{id}', [ReelsController::class, 'reelsViewsUpdate']);
        Route::put('target-page/{id}', [ReelsController::class, 'reelsClicksUpdate']);
        Route::put('update/likes/{id}', [ReelsController::class, 'reelsLikesUpdate']);
        Route::put('update/hearts/{id}', [ReelsController::class, 'reelsHeartsUpdate']);
        Route::post('comments/{id}', [ReelsController::class, 'reelsCommentsList']);
        Route::post('comments/{id}/add', [ReelsController::class, 'reelsCommentsAdd']);
        Route::delete('comments/delete/{id}', [ReelsController::class, 'reelsCommentsDelete']);
        Route::delete('delete/{id}', [ReelsController::class, 'reelsDelete']);

    });


    Route::group(['prefix'=>'category'], function(){
        Route::get('all', [CategoryController::class, 'all']);
        Route::get('filter/{id}', [CategoryController::class, 'filter']);
    });


    Route::get('profile', [UsersController::class, 'profile']);
    Route::post('profile', [UsersController::class, 'profilePost']);

    Route::get('download-video', [DownloadController::class, 'allVideo']);
    

});


