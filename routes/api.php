<?php

use App\Http\Controllers\AddYearController;
use App\Http\Controllers\ApproveIncHdController;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GetQcYear;
use App\Http\Controllers\IncHdController;
use App\Http\Controllers\ManageUserController;
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
    Route::get('user/{id}', [AuthController::class, 'getUser']);


    //Incentive
    Route::group(['prefix' => 'incentive'], function () {

        Route::get('/checkIncHd/{year}/{month}', [IncHdController::class, 'checkIncHd']);
        //Add Year
        Route::post('/add-year',[AddYearController::class,'addYear']);
        Route::get('/list-year',[AddYearController::class,'ListYear']);


        //Add WorkDay
        Route::get('/workday/list/{year}',[QCWorkdayController::class,'list']);
        Route::post('/workday/store',[QCWorkdayController::class,'store']);

        Route::get('/qc_year/{year}', [GetQcYear::class , 'getQcYear']);
        Route::get('/qc_month/{year}/{month}/{status}', [IncHdController::class, 'qc_month']);
        Route::group(['prefix' => 'manage'], function () {
            // จัดการระดับการ QC ใน Table QcTime
            Route::get('/qc_time', [QcTimeController::class, 'index']);
            Route::post('/qc_time_update', [QcTimeController::class, 'update']);
            // จัดการการคำนวณเกรด ใน Table QcRate
            Route::get('/calculate_grade', [QcRateController::class,'getRate']);
            Route::post('/qc_rate_update', [QcRateController::class, 'update']);
        });


        //Insert Data to Database
        Route::post('/qc_month/store', [IncHdController::class, 'store']);
        Route::post('/qc_month/update', [IncHdController::class, 'update']);

        //approve by QC And HR
        Route::post('/qc_month/hr/update',[ApproveIncHdController::class,'ApproveByHR']);
        Route::post('/qc_month/qc/update',[ApproveIncHdController::class,'ApproveByQC']);
        Route::post('/qc_month/hr/confirmpaydate',[ApproveIncHdController::class,'ConfirmPayDate']);

        //User manage
        Route::get('/user-list',[ManageUserController::class,'userList']);
        Route::get('/user-detail/{id}',[ManageUserController::class,'userDetail']);
        Route::post('/user-store',[ManageUserController::class,'userStore']);
        Route::post('/user-update/{id}',[ManageUserController::class,'userUpdate']);
        Route::post('/user-delete/{id}',[ManageUserController::class,'userDelete']);
    });

    //Products
    Route::group(['prefix' => 'product'], function () {
        Route::get('/', [QcProdController::class, 'index']);
        Route::post('/store', [QcProdController::class, 'store']);
        Route::get('edit/{id}', [QcProdController::class, 'edit']);
        Route::post('/update/{id}', [QcProdController::class, 'update']);
        Route::get('/not-found/{year}/{month}', [QcProdController::class, 'notFound']);
    });

});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/signup', [AuthController::class, 'signup']);

Route::get('test',[Controller::class, 'test']);










