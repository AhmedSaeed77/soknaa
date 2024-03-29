<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('clear-cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:cache');
    return "done";
});

Route::get('/storage', function () {
    \Artisan::call('storage:link');
    return "done";
});


Route::get('/tets', function () {
    $order = \App\Models\Order::find(3);
    $order->update(['status' => 1]);
    return "done";
});