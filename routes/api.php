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

    // Transaction
    Route::controller(TransactionController::class)->prefix('transaction')->group(function () {
        Route::post('insert', 'insert');
        Route::post('update/{id}', 'update');
        Route::post('delete/{id}', 'delete');
        Route::get('list', 'list');
        Route::get('show/{id}', 'show');
    });

    // User Auth
    Route::controller(AuthController::class)->prefix('user')->group(function () {
        Route::post('change-password', 'changePassword')->name('change-password');
        Route::get('user-profile/{id}', 'userProfile')->name('user-profile');
        Route::get('account-details/{id}', 'accountDetails')->name('account-detail');
    });
});


// User Guest

Route::controller(UserController::class)->prefix('user')->group(function () {
    Route::post('/create', 'create')->name('create');
    Route::post('/login', 'login')->name('login');
    Route::get('/verify-email/{email_verification_code}', 'verifyEmail')->name('verify-email');
    Route::post('/forgotpassword-link', 'forgotPasswordLink')->name('forgotpassword-link');
    Route::post('/forgot-password', 'forgotPassword')->name('forgot-password');
});
