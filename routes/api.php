<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\api\StandardController;
use App\Http\Controllers\api\SiteController;
use App\Http\Controllers\api\OrderController;
use App\Http\Controllers\api\ChatMobileController;
use App\Http\Controllers\dashboard\UserController;
use App\Http\Controllers\dashboard\OrderDashboardController;
use App\Http\Controllers\dashboard\IndexDashboardController;
use App\Http\Controllers\dashboard\ChatController;
use App\Http\Controllers\dashboard\PrivateChatController;
use App\Http\Controllers\dashboard\MailController;
use App\Http\Controllers\api\ChatPusherController;
use App\Http\Controllers\api\ComplaintController;
use App\Http\Controllers\api\PrivateChatMobileController;
use App\Http\Controllers\dashboard\ComplaintDashboardController;

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


        Route::post('sendMail', [MailController::class, 'sendMail']);

        Route::post('login', [AdminController::class, 'login']);

        Route::group(['middleware' => 'auth:admin'], function () {
            Route::resource('admins', AdminController::class);
            Route::post('admin/block/{id}', [AdminController::class, 'block']);

            Route::get('getRequestsToJoin', [UserController::class, 'getRequestsToJoin']);
            Route::get('getOneRequestToJoin/{id}', [UserController::class, 'getOneRequestToJoin']);
            Route::post('acceptReject', [UserController::class, 'acceptReject']);

            Route::get('getAllMembersRemoved', [UserController::class, 'getAllMembersRemoved']);

            Route::get('getAllMembers', [UserController::class, 'getAllMembers']);
            Route::get('getOneMember/{id}', [UserController::class, 'getOneMember']);
            Route::get('block/{id}', [UserController::class, 'block']);
            Route::post('deleteMember/{id}', [UserController::class, 'deleteMember']);

            Route::get('getAllorders', [OrderDashboardController::class, 'getAllorders']);
            Route::get('getOneOrder/{id}', [OrderDashboardController::class, 'getOneOrder']);
            Route::post('changestatus/{id}', [OrderDashboardController::class, 'changestatus']);

            Route::get('order/close/{id}', [OrderDashboardController::class, 'OrderClose']);

            Route::get('getAllComplaints', [ComplaintDashboardController::class, 'getAllComplaints']);
            Route::get('getOneComplaint/{id}', [ComplaintDashboardController::class, 'getOneComplaint']);
            Route::post('block/user/{id}', [ComplaintDashboardController::class, 'blockUser']);

            Route::get('index', [IndexDashboardController::class, 'index']);

            Route::post('create/message', [ChatController::class, 'create']);
            Route::get('getAllMessagesForUser/{id}/{order_id}', [ChatController::class, 'getAllMessagesForUser']);

            Route::get('getAllMessagesOrders', [ChatController::class, 'getAllMessagesOrders']);
            Route::get('changeAllMessagesForUser/{id}/{order_id}', [ChatController::class, 'changeAllMessagesForUser']);

            Route::get('getAllPrivateCaht', [PrivateChatController::class, 'getAllPrivateCaht']);
            Route::get('getAllPrivateMessagesForUser/{id}', [PrivateChatController::class, 'getAllPrivateMessagesForUser']);
            Route::post('createPrivateMessagesForUser', [PrivateChatController::class, 'createPrivateMessagesForUser']);
            Route::get('changeAllPrivateMessagesForUser/{id}', [PrivateChatController::class, 'changeAllPrivateMessagesForUser']);


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
    Route::get('getAllCountries', [StandardController::class, 'getAllCountries']);
    Route::get('getAllCitiesByCountry/{id}', [StandardController::class, 'getAllCitiesByCountry']);

    Route::get('/getAllUsersNotAut', [SiteController::class, 'getAllUsersNotAut']);

    Route::group(['middleware' => ['auth' , 'check.blocked']], function () {

        Route::post('/deleteaccount', [UserAuthController::class, 'deleteaccount']);

        Route::post('/adduser', [UserAuthController::class, 'adduser']);
        Route::get('/getAllUserByFinance', [UserAuthController::class, 'getAllUserByFinance']);
        Route::get('/getOneUser/{id}', [UserAuthController::class, 'getOneUser']);
        Route::post('/showProfile', [UserAuthController::class, 'isShowProfile']);

        Route::get('getUser', [UserAuthController::class, 'getUser']);
        Route::get('changeOline', [UserAuthController::class, 'changeOline']);
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

        Route::post('createmessage', [ChatMobileController::class, 'createmessage']);
        Route::get('getAllMessagesForUser/{order_id}', [ChatMobileController::class, 'getAllMessagesForUser']);

        Route::post('createprivatemessage', [PrivateChatMobileController::class, 'createmessage']);
        Route::get('getAllPrivateMessagesForUser', [PrivateChatMobileController::class, 'getAllMessagesForUser']);

        Route::post('chats/provide', [ChatPusherController::class,'provide']);
        Route::post('chats/rooms/{rooms:id}/send', [ChatPusherController::class,'send']);
        Route::get('chats/rooms/getRooms', [ChatPusherController::class,'getRooms']);
        Route::get('chats/rooms/{id}/getMessages', [ChatPusherController::class,'getMessages']);

        Route::get('chats/rooms/delete/{id}', [ChatPusherController::class,'deleteRoome']);

        Route::get('chats/rooms/block/{id}', [ChatPusherController::class,'blockRoom']);

        Route::post('complaints', [ComplaintController::class,'store']);

    });


});
