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
use \App\Http\Controllers\Api\CompanyProductController;
use \App\Http\Controllers\Api\LeadGenerateController;
use \App\Http\Controllers\Api\MixController;
use \App\Http\Controllers\Api\CityController;
use \App\Http\Controllers\Api\DeckTypeController;
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
        Route::get('get-seller-list','getSellerList');
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
        Route::post('search-product','searchProduct');
        Route::middleware('is_admin')->group(function (){
            Route::post('add-product','store');
            //Route::post('edit-product/{id}','edit');
        });
    });
    Route::controller(LeadGenerateController::class)->group(function (){
       Route::post('run-estimate','runEstimate');
       Route::post('edit-run-estimate/{id}','editRunEstimate');
       Route::post('add-lead-price','addLeadPrice');
    });
    Route::controller(CityController::class)->group(function (){
       Route::get('get-city-list','index');
       Route::middleware('is_admin')->group(function (){
          Route::post('add-city','store');
          Route::post('edit-city/{id}','update');
          Route::post('delete-city/{id}','delete');
       });
    });
    Route::controller(DeckTypeController::class)->group(function (){
        Route::get('get-deck-type-list','index');
        Route::middleware('is_admin')->group(function (){
            Route::post('add-deck-type','store');
            Route::post('edit-deck-type/{id}','update');
            Route::post('delete-deck-type/{id}','delete');
        });
    });
});
