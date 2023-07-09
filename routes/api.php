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
use \App\Http\Controllers\Api\CustomerPaymentController;
use \App\Http\Controllers\Api\InspectionResultController;
use \App\Http\Controllers\Api\OtherController;
use \App\Http\Controllers\Api\ClientReportController;
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

Route::get('test',[\App\Http\Controllers\TestController::class,'index']);
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
            Route::post('delete-user/{id}','delete');
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
        Route::post('delete-job-type/{id}','delete');
    });
    Route::controller(CompanyController::class)->group(function (){
        Route::get('get-company','index');
        Route::middleware('is_admin')->group(function (){
           Route::post('add-company','store');
           Route::post('edit-company/{id}','edit');
           Route::post('delete-company/{id}','delete');
        });
    });
    Route::controller(ProductController::class)->group(function (){
        Route::get('get-product','index');
        Route::get('product-details/{id}','productDetails');
        Route::get('search-product','searchProduct');
        Route::get('product-list','productList');
        Route::middleware('is_admin')->group(function (){
            Route::post('add-product','store');
            Route::post('edit-product/{id}','edit');
            Route::post('delete-product/{id}','delete');
        });
        Route::get('get-default-product','getDefaultProduct');
        Route::post('search-default-product','searchDefaultProduct');
        Route::get('get-product-own-category-list','productOwnCategory');
    });
    Route::controller(LeadGenerateController::class)->group(function (){
       Route::post('run-estimate','runEstimate');
       Route::get('run-estimate-details/{id}','runEstimateDetails');
       Route::post('edit-run-estimate/{id}','editRunEstimate');
       Route::post('add-lead-price','addLeadPrice');
       Route::post('edit-lead-details','editLeadDetails');
       Route::post('low-price-company','lowPriceCompany');
       Route::post('approved-combination','approvedCombination');
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
    Route::controller(CustomerPaymentController::class)->group(function (){
       Route::get('get-customer-payment','index');
       Route::post('add-customer-payment','create');
       Route::post('edit-customer-payment/{id}','edit');
       Route::post('delete-customer-payment','delete');
    });
    Route::controller(InspectionResultController::class)->group(function (){
        Route::get('get-inspections-result','index');
        Route::post('add-inspections-result','create');
        Route::post('edit-inspections-result/{id}','edit');
        Route::post('delete-inspections-result','delete');
    });
    Route::controller(OtherController::class)->group(function (){
        Route::get('get-other-company','index');
        Route::middleware('is_admin')->group(function () {
            Route::post('add-other-company','create');
            Route::post('edit-other-company/{id}','update');
            Route::post('delete-other-company','delete');
        });
    });
    Route::controller(ClientReportController::class)->group(function (){
        Route::get('get-client-report','index');
        Route::post('add-update-client-report','addUpdate');
        Route::get('get-supplier-list','getSupplierList');
        Route::post('add-supplier','addSupplier');
        Route::post('edit-supplier/{id}','editSupplier');
        Route::post('delete-supplier/{id}','deleteSupplier');
    });
});
