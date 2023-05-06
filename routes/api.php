<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\AuthController;
use \App\Http\Controllers\Api\ApiController;
use \App\Http\Controllers\Api\LeadControllerController;
use \App\Http\Controllers\Api\SummearyController;
use \App\Http\Controllers\Api\UserController;
use \App\Http\Controllers\Api\DashboardController;
use \App\Http\Controllers\Api\JobTypeController;
use \App\Http\Controllers\Api\CompanyController;
use \App\Http\Controllers\Api\ProductController;
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
        //Route::post('register','register');
    });
});

Route::middleware('auth:api')->group(function (){
    Route::controller(AuthController::class)->group(function (){
        Route::get('get-current-user-info','getCurrentUserInfo');
    });
    Route::controller(LeadControllerController::class)->group(function (){
        Route::get('get-lead','getLead');
        Route::post('add-lead','addLead');
        Route::post('edit-lead/{id}','editLead');
        Route::get('lead-details/{id}','leadDetails');
    });
    Route::controller(SummearyController::class)->group(function (){
        Route::get('status-wise-summary','index');
        Route::get('job-type-sales-summary','jobTypeSalesSummary');
    });
    Route::controller(UserController::class)->group(function (){
        Route::middleware('is_admin')->group(function (){
            Route::post('add-user','store');
            Route::get('get-user','index');
            Route::post('edit-user','edit');
        });
        Route::post('profile-update','profileUpdate');
    });
    Route::controller(DashboardController::class)->group(function (){
        Route::get('dashboard','index');
        Route::get('job-type-pie-chart','jobTypePieChart');
        Route::get('sales-bar-chart','salesBarChart');
    });
    Route::controller(JobTypeController::class)->group(function (){
        Route::get('get-job-type','index');
        Route::post('add-job-type','store');
        Route::post('edit-job-type','edit');
    });
    Route::controller(CompanyController::class)->group(function (){
        Route::get('get-company','index');
        Route::middleware('is_admin')->group(function (){
           Route::post('add-company','store');
           Route::post('edit-company/{id}','edit');
        });
    });
    Route::controller(ProductController::class)->group(function (){
        Route::get('get-product','index');
        Route::middleware('is_admin')->group(function (){
            Route::post('add-product','store');
            Route::post('edit-product/{id}','edit');
        });
    });
});
