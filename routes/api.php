<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\api\StandardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('user', function (Request $request) {
    return $request->user();
});

///////////////////////////////// dashboard  //////////////////////////////

Route::group(['middleware' => 'Lang','prefix' => 'admin'], function () {

    Route::post('login', [AdminController::class, 'login']);

    Route::group(['middleware' => 'auth:admin'], function () {
        Route::resource('admins', AdminController::class);
        Route::post('admin/block/{id}', [AdminController::class, 'block']);
    });
});



///////////////////////////////// mobile  //////////////////////////////


Route::group(['middleware' => 'Lang'], function () {

    Route::post('register', [UserAuthController::class, 'register']);

    Route::post('login', [UserAuthController::class, 'login']);
    Route::post('/reset', [UserAuthController::class, 'reset']);
    Route::post('/resetUserconfirm', [UserAuthController::class, 'resetUserconfirm']);
    Route::post('/changePassword', [UserAuthController::class, 'changePassword']);

    Route::get('standard', [StandardController::class, 'standard']);


    Route::group(['middleware' => 'auth'], function () {

        Route::post('/adduser', [UserAuthController::class, 'adduser']);
        Route::get('/getAllUserByFinance', [UserAuthController::class, 'getAllUserByFinance']);

    });

    
});