<?php

use Illuminate\Support\Facades\Route;
use Google\Client as GoogleClient;

use Illuminate\Support\Facades\Http;
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


Route::get('/testnotification', function () {

    $fcm = "f302IpfbT3WRIs1mHc8BW1:APA91bHmmPcwv1fY-5a-e0ODK3fnvUsUF9Gtv4N4tV-oXTnBZiBkMC-7fAoDvogrZ0MAmGkY7w3-X_-G48h0Xm6wZvu2C9j5EZKP5ilZLOAOAoYHEOJhuEyCbHR_CX4Ep1UKZOVsFw4-";

    $title = "اشعار جديد";
    $description = "تيست تيست تيست";

//    $credentialsFilePath = "json/file.json";  // local
    $credentialsFilePath = Http::get(asset('json/sknoaa-app-2024-fa6a3cebd295.json'));
    
    $client = new GoogleClient();
    $client->setAuthConfig($credentialsFilePath);
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    $client->refreshTokenWithAssertion();
    $token = $client->getAccessToken();

    $access_token = $token['access_token'];

    $headers = [
        "Authorization: Bearer $access_token",
        'Content-Type: application/json'
    ];

    $data = [
        "message" => [
            "token" => $fcm,
            "notification" => [
                "title" => $title,
                "body" => $description,
            ],
        ]
    ];
    $payload = json_encode($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/sknoaa-app-2024/messages:send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output for debugging
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return response()->json([
            'message' => 'Curl Error: ' . $err
        ], 500);
    } else {
        return response()->json([
            'message' => 'Notification has been sent',
            'response' => json_decode($response, true)
        ]);
    }
})->name('testnotification');


// Route::get('/testnotification', function () {
//     $fcm = "cmMH_MU4QemcpOxdNU8Wej:APA91bHYIDZpvp5BAk_JrZfXsW-xiKVUZOQvhWst6_b3TMyZJrXgnSFOLYInWWB9VbSJNUNepJ2JPJ5_J5mxRbrre7mS46kM1YK9N3mxa5nNCKHxdezY24LDKdAC4uHzI0eqtMpi6AVv";
//     $title = "اشعار جديد";
//     $description = "تيست تيست تيست";

//     $credentialsFilePath = Http::get(asset('json/sknoaa-app-2024-41115e8611db.json'));
    
//     $client = new GoogleClient();
//     $client->setAuthConfig($credentialsFilePath);
//     $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
//     // $client->refreshTokenWithAssertion();
//     $token = $client->getAccessToken();

//     $access_token = $token['access_token'];

//     $headers = [
//         "Authorization: Bearer $access_token",
//         'Content-Type: application/json'
//     ];

//     $data = [
//         "message" => [
//             "token" => $fcm,
//             "notification" => [
//                 "title" => $title,
//                 "body" => $description,
//             ],
//         ]
//     ];
//     $payload = json_encode($data);

//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/sknoaa-app-2024/messages:send');
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
//     curl_setopt($ch, CURLOPT_VERBOSE, true);
//     $response = curl_exec($ch);
//     $err = curl_error($ch);
//     curl_close($ch);

//     if ($err) {
//         return response()->json([
//             'message' => 'Curl Error: ' . $err
//         ], 500);
//     } else {
//         return response()->json([
//             'message' => 'Notification has been sent',
//             'response' => json_decode($response, true)
//         ]);
//     }
// })->name('testnotification2');


// Route::get('/testnotification', function () {


//     $fcm = "cmMH_MU4QemcpOxdNU8Wej:APA91bHYIDZpvp5BAk_JrZfXsW-xiKVUZOQvhWst6_b3TMyZJrXgnSFOLYInWWB9VbSJNUNepJ2JPJ5_J5mxRbrre7mS46kM1YK9N3mxa5nNCKHxdezY24LDKdAC4uHzI0eqtMpi6AVv";
//     $title = "اشعار جديد";
//     $description = "تيست تيست تيست";


//     $credentialsFilePath = Http::get(asset('json/soknaa-a135f-a9d53965024b.json'));
   
//     $client = new GoogleClient();
//     $client->setAuthConfig($credentialsFilePath);
//     $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
//     $client->refreshTokenWithAssertion();
//     $token = $client->getAccessToken();

//     $access_token = $token['access_token'];

//     $headers = [
//         "Authorization: Bearer $access_token",
//         'Content-Type: application/json'
//     ];

//     $data = [
//         "message" => [
//             "token" => $fcm,
//             "notification" => [
//                 "title" => $title,
//                 "body" => $description,
//             ],
//         ]
//     ];
//     $payload = json_encode($data);

//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/soknaa-a135f/messages:send');
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
//     curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output for debugging
//     $response = curl_exec($ch);
//     $err = curl_error($ch);
//     curl_close($ch);

//     if ($err) {
//         return response()->json([
//             'message' => 'Curl Error: ' . $err
//         ], 500);
//     } else {
//         return response()->json([
//             'message' => 'Notification has been sent',
//             'response' => json_decode($response, true)
//         ]);
//     }
// })->name('testnotification2');

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