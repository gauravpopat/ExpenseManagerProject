<?php

use App\Http\Controllers\Auth\AccountController;
use App\Http\Controllers\Auth\AccountUsersController;
use App\Http\Controllers\Auth\TransactionController;
use App\Http\Controllers\Auth\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

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

Route::middleware(['auth:sanctum'])->group(function () {
    //Account
        Route::controller(AccountController::class)->prefix('account')->group(function () {
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
            Route::get('list', 'list');
            Route::get('show/{id}', 'show');
            Route::post('insert', 'insert');
        });

    //Account_Users
        Route::controller(AccountUsersController::class)->prefix('accountusers')->group(function () {
            Route::post('insert', 'insert');
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
            Route::get('list', 'list');
            Route::get('show/{id}', 'show');
        });

    //Transaction
        Route::controller(TransactionController::class)->prefix('transaction')->group(function () {
            Route::post('insert','insert');
            Route::post('update/{id}','update');
            Route::post('delete/{id}','delete');
            Route::get('list','list');
            Route::get('show/{id}','show');
        });
});

//User Table

Route::controller(UserController::class)->prefix('user')->group(function(){
    Route::post('/create','create')->name('create');
    Route::get('/verify/{email_verification_code}','verify')->name('verify');
    Route::post('/login','login')->name('login');
    Route::post('/forgotpassword','forgotPassword')->name('forgotpassword');
    Route::post('/forgotpw','forgotPw')->name('forgotpw');
    Route::post('changePassword','changePassword')->name('changePassword')->middleware('auth:sanctum');
    Route::get('userprofile/{id}','userProfile')->name('userProfile')->middleware('auth:sanctum');
});


