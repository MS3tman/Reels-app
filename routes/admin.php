<?php

use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\CountriesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix'=>'country'], function(){
    Route::post('create', [CountriesController::class, 'create']);
    Route::get('read', [CountriesController::class, 'read']);
    Route::delete('delete/{id}', [CountriesController::class, 'delete']);
});

Route::group(['prefix'=>'category'], function(){
    Route::post('create', [CategoriesController::class, 'create']);
    Route::get('read', [CategoriesController::class, 'read']);
    Route::delete('delete/{id}', [CategoriesController::class, 'delete']);
});