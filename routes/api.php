<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Login;
use App\Http\Controllers\Register;
use Illuminate\Support\Facades\Auth;
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

Route::post('/register', [Register::class,'register'])->name('register');
Route::post('/login', [Login::class,'login'])->name('login');
Route::get('/category', [CategoryController::class,'showAll']);
Route::middleware('auth:api')->group(function() {
    Route::put('/createCategory', [CategoryController::class, 'create']);
});