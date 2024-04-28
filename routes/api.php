<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
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
//Route::pattern('id', '[0-9]');


Route::get('country/all', [HomeController::class, 'CountriesList']);
Route::get('category/all', [HomeController::class, 'CategoriesList']);



Route::prefix('auth')->middleware('guest:sanctum')->group( function(){
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [LoginController::class, 'register']);
    Route::get('register-verify/{token}', [LoginController::class, 'verifyRegister'])->name('verify_register');
    Route::get('register-retry/{token}', [LoginController::class, 'retryRegister'])->name('retry_register');
    Route::post('forget-password', [LoginController::class, 'forgetPassword']);
    Route::post('check-code/{token}', [LoginController::class, 'checkCode'])->name('check_code');;    
    Route::post('reset-password/{token}', [LoginController::class, 'resetPassword'])->name('reset_password');    
});

Route::middleware('auth:sanctum')->group(function(){

    Route::post('logout', [LoginController::class, 'logout']);

    Route::group(['prefix'=>'reel'], function(){
        Route::post('create/chunk', [ReelsController::class, 'uploadChunks']);
        
        
        Route::post('comments/{reelId}', [ReelsController::class, 'reelsCommentsList']);
        Route::post('comments/{reelId}/add', [ReelsController::class, 'reelsCommentsAdd']);
        Route::delete('comments/{reelId}/delete/{id}', [ReelsController::class, 'reelsCommentsDelete']);
        Route::put('update/wishlist/{id}', [ReelsController::class, 'reelsWishlistUpdate']);
        
        // Route::put('target-page/{id}', [ReelsController::class, 'reelsClicksUpdate']);
        // Route::put('update/views/{id}', [ReelsController::class, 'reelsViewsUpdate']);
        // Route::put('update/likes/{id}', [ReelsController::class, 'reelsLikesUpdate']);
        // Route::put('update/hearts/{id}', [ReelsController::class, 'reelsHeartsUpdate']);

        // Route::get('user/list', [ReelsController::class, 'reelsListForUser']);
        // Route::get('user/{id}', [ReelsController::class, 'reelsByIdForUser']);
        // Route::get('{id}', [ReelsController::class, 'reelsById']);
        // Route::get('list', [ReelsController::class, 'reelsList']);
        // Route::put('update/{id}', [ReelsController::class, 'reelsUpdate']);
        // Route::delete('delete/{id}', [ReelsController::class, 'reelsDelete']);
        Route::get('list', [ReelsController::class, 'reelList']);
        Route::post('', [ReelsController::class, 'ReelAddNew']);
        Route::post('coupon', [ReelsController::class, 'ReelAddNewCoupon']);
        Route::post('views', [ReelsController::class, 'CampainAddViews']);
        Route::post('heart', [ReelsController::class, 'CampainToggleHeart']);
        Route::post('like', [ReelsController::class, 'CampainToggleLike']);
    });



    
    

});


