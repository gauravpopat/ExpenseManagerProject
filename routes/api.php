<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountUsersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
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

Route::middleware(['auth:sanctum'])->group(function () {
    //Account
    Route::controller(AccountController::class)->prefix('account')->group(function () {
        Route::get('list', 'list');
        Route::post('insert', 'insert');
        Route::post('update/{id}', 'update');
        Route::get('show/{id}', 'show');
        Route::post('delete/{id}', 'delete');
        Route::get('account-details/{id}', 'accountDetails')->name('account-detail');
    });

    //Account_Users
    Route::controller(AccountUsersController::class)->prefix('accountusers')->group(function () {
        Route::get('list', 'list');
        Route::post('insert', 'insert');
        Route::post('update/{id}', 'update');
        Route::get('show/{id}', 'show');
        Route::post('delete/{id}', 'delete');
    });

    // Transaction
    Route::controller(TransactionController::class)->prefix('transaction')->group(function () {
        Route::get('list', 'list');
        Route::post('insert', 'insert');
        Route::post('update/{id}', 'update');
        Route::get('show/{id}', 'show');
        Route::post('delete/{id}', 'delete');
    });

    // User Auth
    Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::post('change-password', 'changePassword')->name('change-password');
        Route::get('user-profile', 'userProfile')->name('user-profile');
    });
});


// User Guest

Route::controller(AuthController::class)->prefix('user')->group(function () {
    Route::post('/create', 'create')->name('create');
    Route::post('/login', 'login')->name('login');
    Route::get('/verify-email/{email_verification_code}', 'verifyEmail')->name('verify-email');
    Route::post('/forgotpassword-link', 'forgotPasswordLink')->name('forgotpassword-link');
    Route::post('/forgot-password', 'forgotPassword')->name('forgot-password');
    
});
