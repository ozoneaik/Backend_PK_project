<?php

use App\Http\Controllers\AddYearController;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\GetQcYear;
use App\Http\Controllers\IncHdController;
use App\Http\Controllers\QcProdController;
use App\Http\Controllers\QcRateController;
use App\Http\Controllers\QcTimeController;
use App\Http\Controllers\QcWorkdayController;

use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);


    //Incentive
    Route::group(['prefix' => 'incentive'], function () {
        //Add Year
        Route::post('/add-year',[AddYearController::class,'addYear']);
        Route::get('/list-year',[AddYearController::class,'ListYear']);


        Route::get('/qc_year/{year}', [GetQcYear::class , 'getQcYear']);
        Route::get('/qc_month/{year}/{month}', [IncHdController::class, 'qc_month']);
        Route::group(['prefix' => 'manage'], function () {
            // จัดการระดับการ QC ใน Table QcTime
            Route::get('/qc_time', [QcTimeController::class, 'index']);
            // จัดการการคำนวณเกรด ใน Table QcRate
            Route::get('/calculate_grade', [QcRateController::class,'getRate']);
        });


        //Insert Data to Database
        Route::post('/qc_month/store', [IncHdController::class, 'store']);
    });

    //Products
    Route::group(['prefix' => 'product'], function () {
        Route::get('/', [QcProdController::class, 'index']);
        Route::post('/store', [QcProdController::class, 'store']);
        Route::get('edit/{id}', [QcProdController::class, 'edit']);
        Route::post('/update/{id}', [QcProdController::class, 'update']);
    });

    Route::group(['prefix' => 'workday'], function () {
        Route::get('/index', [QcWorkdayController::class, 'index']);
        Route::post('/store', [QcWorkdayController::class, 'store']);
        Route::get('get-years', [QcWorkdayController::class, 'getYears']);
    });

});

Route::post('/login', [AuthController::class, 'login']);






