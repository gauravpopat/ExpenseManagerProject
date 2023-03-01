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

Route::post('/create',[HomeController::class,'create'])->name('create');
Route::get('/verify/{email_verification_code}',[HomeController::class,'verify'])->name('verify');
Route::post('/login',[HomeController::class,'login'])->name('login');


//Crud For Account Table

Route::post('update/{id}',[HomeController::class,'update'])->name('update');

Route::post('delete/{id}',[HomeController::class,'delete'])->name('delete');

Route::get('list',[HomeController::class,'list'])->name('list');

Route::get('show/{id}',[HomeController::class,'show'])->name('show');

Route::post('insert',[HomeController::class,'insert'])->name('insert');



//Crud For Account_Users Table
Route::post('auinsert',[HomeController::class,'auinsert'])->name('auinsert');

Route::post('auupdate/{id}',[HomeController::class,'auupdate'])->name('auupdate');

Route::post('audelete/{id}',[HomeController::class,'audelete'])->name('audelete');

Route::get('aulist',[HomeController::class,'aulist'])->name('aulist');

Route::get('aushow/{id}',[HomeController::class,'aushow'])->name('aushow');


//Crud For Transaction Table
Route::post('tinsert',[HomeController::class,'tinsert'])->name('tinsert');

Route::post('tupdate/{id}',[HomeController::class,'tupdate'])->name('tupdate');

Route::post('tdelete/{id}',[HomeController::class,'tdelete'])->name('tdelete');

Route::get('tlist',[HomeController::class,'tlist'])->name('tlist');

Route::get('tshow/{id}',[HomeController::class,'tshow'])->name('tshow');

