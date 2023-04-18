<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\AuthController;
use \App\Http\Controllers\Api\ApiController;
use \App\Http\Controllers\Api\LeadControllerController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('guest')->group(function (){
    Route::controller(AuthController::class)->group(function (){
        Route::post('login','login');
        Route::post('register','register');
    });
});
Route::middleware('auth:api')->group(function (){
    Route::controller(LeadControllerController::class)->group(function (){
       Route::post('add-lead','addLead');
    });
});
