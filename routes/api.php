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
    Route::prefix('account')->group(function () {
        Route::controller(AccountController::class)->group(function () {
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
            Route::get('list', 'list');
            Route::get('show/{id}', 'show');
            Route::post('insert', 'insert');
        });
    });

    //Account_Users
    Route::prefix('accountusers')->group(function () {
        Route::controller(AccountUsersController::class)->group(function () {
            Route::post('insert', 'insert');
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
            Route::get('list', 'list');
            Route::get('show/{id}', 'show');
        });
    });

    //Transaction
    Route::prefix('transaction')->group(function () {
        Route::controller(TransactionController::class)->group(function () {
            Route::post('insert','insert');
            Route::post('update/{id}','update');
            Route::post('delete/{id}','delete');
            Route::get('list','list');
            Route::get('show/{id}','show');
           
        });
    });
});

//User Table
Route::post('/create', [UserController::class, 'create'])->name('create');
Route::get('/verify/{email_verification_code}', [UserController::class, 'verify'])->name('verify');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/forgotpassword', [UserController::class, 'forgotPassword'])->name('forgotpassword');
Route::post('/forgotpw', [UserController::class, 'forgotPw'])->name('forgotpw');
Route::post('changePassword', [UserController::class, 'changePassword'])->name('changePassword')->middleware('auth:sanctum');
Route::get('userprofile/{id}',[UserController::class, 'userProfile'])->name('userProfile')->middleware('auth:sanctum');