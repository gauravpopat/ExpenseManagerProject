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

Route::get('show/{id}',[HomeController::class,'show'])->name('show');

Route::post('insert',[HomeController::class,'insert'])->name('insert');