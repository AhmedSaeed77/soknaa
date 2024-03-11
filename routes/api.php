<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\api\StandardController;
use App\Http\Controllers\api\SiteController;
use App\Http\Controllers\api\OrderController;
use App\Http\Controllers\dashboard\UserController;
use App\Http\Controllers\dashboard\OrderDashboardController;
use App\Http\Controllers\dashboard\IndexDashboardController;

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
Route::group(['middleware' => 'cors'], function () {

    Route::group(['middleware' => 'Lang','prefix' => 'admin'], function () {


        Route::post('login', [AdminController::class, 'login']);

        Route::group(['middleware' => 'auth:admin'], function () {
            Route::resource('admins', AdminController::class);
            Route::post('admin/block/{id}', [AdminController::class, 'block']);

            Route::get('getRequestsToJoin', [UserController::class, 'getRequestsToJoin']);
            Route::get('getOneRequestToJoin/{id}', [UserController::class, 'getOneRequestToJoin']);
            Route::post('acceptReject', [UserController::class, 'acceptReject']);

            Route::get('getAllMembers', [UserController::class, 'getAllMembers']);
            Route::get('getOneMember/{id}', [UserController::class, 'getOneMember']);
            Route::get('block/{id}', [UserController::class, 'block']);
            Route::post('deleteMember/{id}', [UserController::class, 'deleteMember']);

            Route::get('getAllorders', [OrderDashboardController::class, 'getAllorders']);
            Route::get('getOneOrder/{id}', [OrderDashboardController::class, 'getOneOrder']);
            Route::post('changestatus/{id}', [OrderDashboardController::class, 'changestatus']);

            Route::get('index', [IndexDashboardController::class, 'index']);


        });
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
    Route::get('getAllCitiesByCountry/{id}', [StandardController::class, 'getAllCitiesByCountry']);

    Route::get('/getAllUsersNotAut', [SiteController::class, 'getAllUsersNotAut']);

    Route::group(['middleware' => 'auth'], function () {

        Route::post('/adduser', [UserAuthController::class, 'adduser']);
        Route::get('/getAllUserByFinance', [UserAuthController::class, 'getAllUserByFinance']);
        Route::get('/getOneUser/{id}', [UserAuthController::class, 'getOneUser']);
        Route::post('/showProfile', [UserAuthController::class, 'isShowProfile']);

        Route::get('getUser', [UserAuthController::class, 'getUser']);
        Route::post('updateUserName', [UserAuthController::class, 'updateUserName']);
        Route::post('updateUserPhone', [UserAuthController::class, 'updateUserPhone']);
        Route::post('updateUserEmail', [UserAuthController::class, 'updateUserEmail']);
        Route::post('updateUserPassword', [UserAuthController::class, 'updateUserPassword']);
        Route::post('updateUserLocation', [UserAuthController::class, 'updateUserLocation']);
        Route::post('updateUserMaritalStatus', [UserAuthController::class, 'updateUserMaritalStatus']);
        Route::post('updateUserDescription', [UserAuthController::class, 'updateUserDescription']);
        Route::post('updateUserReligiousCommitment', [UserAuthController::class, 'updateUserReligiousCommitment']);
        Route::post('updateUserStudyAndWork', [UserAuthController::class, 'updateUserStudyAndWork']);
        Route::post('updateUserAbout', [UserAuthController::class, 'updateUserAbout']);
        Route::post('updateUserLifePartnerInfo', [UserAuthController::class, 'updateUserLifePartnerInfo']);

        Route::post('deleteImage', [UserAuthController::class, 'deleteIMage']);
        Route::post('storeImage', [UserAuthController::class, 'storeImage']);


        Route::get('/getAllUsers', [SiteController::class, 'getAllUsers']);
        Route::get('/getAllUsersHome', [SiteController::class, 'getAllUsersHome']);
        Route::get('/getOneUserSite/{id}', [SiteController::class, 'getOneUserSite']);

        Route::post('/search', [SiteController::class, 'search']);

        Route::post('createOrder', [OrderController::class, 'createOrder']);
        Route::get('getAllOrders', [OrderController::class, 'getAllOrders']);
        Route::get('getOneOrder/{id}', [OrderController::class, 'getOneOrder']);

        Route::get('getAllOrdersSuccess', [OrderController::class, 'getAllOrdersSuccess']);

    });


});
