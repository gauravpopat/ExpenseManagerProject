<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//User Table

Route::post('/create',[HomeController::class,'create'])->name('create');
Route::get('/verify/{email_verification_code}',[HomeController::class,'verify'])->name('verify');
Route::post('/login',[HomeController::class,'login'])->name('login');
Route::post('/forgotpassword',[HomeController::class,'forgotPassword'])->name('forgotpassword');
Route::post('/forgotpw',[HomeController::class,'forgotPw'])->name('forgotpw');
Route::post('changePassword',[HomeController::class,'changePassword'])->name('changePassword')->middleware('auth:sanctum');


//Crud For Account Table

Route::post('update/{id}',[HomeController::class,'update'])->name('update')->middleware('auth:sanctum');

Route::post('delete/{id}',[HomeController::class,'delete'])->name('delete')->middleware('auth:sanctum');

Route::get('list',[HomeController::class,'list'])->name('list')->middleware('auth:sanctum');

Route::get('show/{id}',[HomeController::class,'show'])->name('show')->middleware('auth:sanctum');

Route::post('insert',[HomeController::class,'insert'])->name('insert')->middleware('auth:sanctum');



//Crud For Account_Users Table
Route::post('auinsert',[HomeController::class,'auInsert'])->name('auinsert')->middleware('auth:sanctum');

Route::post('auupdate/{id}',[HomeController::class,'auUpdate'])->name('auupdate')->middleware('auth:sanctum');

Route::post('audelete/{id}',[HomeController::class,'auDelete'])->name('audelete')->middleware('auth:sanctum');

Route::get('aulist',[HomeController::class,'auList'])->name('aulist')->middleware('auth:sanctum');

Route::get('aushow/{id}',[HomeController::class,'auShow'])->name('aushow')->middleware('auth:sanctum');


//Crud For Transaction Table
Route::post('tinsert',[HomeController::class,'tInsert'])->name('tinsert')->middleware('auth:sanctum');

Route::post('tupdate/{id}',[HomeController::class,'tUpdate'])->name('tupdate')->middleware('auth:sanctum');

Route::post('tdelete/{id}',[HomeController::class,'tDelete'])->name('tdelete')->middleware('auth:sanctum');

Route::get('tlist',[HomeController::class,'tList'])->name('tlist')->middleware('auth:sanctum');

Route::get('tshow/{id}',[HomeController::class,'tShow'])->name('tshow')->middleware('auth:sanctum');

