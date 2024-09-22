<?php

namespace App\Http\Controllers\api;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\api\UserResource;
use App\Http\Resources\api\OneUserResource;
use App\Traits\GeneralTrait;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use App\Models\ChatRoomMessage;
use Illuminate\Support\Facades\Auth;

class SiteController extends Controller
{
    use GeneralTrait;

    public function getAllUsers(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $type = ($user->type == 'زوج') ? 'زوجه' : 'زوج';
        $users = User::where('type',$type)
                        ->where('is_showprofile',1)
                        ->where('block',0)
                        ->where('is_active',1)
                        ->where('is_removed',0)
                        ->when($request->has('country'), function ($query) use ($request) {
                $query->whereHas('location', function ($subQuery) use ($request) {
                    $subQuery->where('country', $request->input('country'));
                });
            })
            ->orderBy('is_online','desc')
            ->get();
        $user->update(['last_seen' => \Carbon\Carbon::now(),'is_online' => 1]);
        User::where('is_online', 1)
        ->where('updated_at', '<', now()->subMinutes(3))
        ->update(['is_online' => 0]);
        // if($user->block == 1)
        // {
        //     $user->logout();
        // }
        $title = "تحذير";
        $content1 = 'لقد قمت بإرسال رقم هاتفك لاكثر من 5 مرات سوف يتم حظرك';
        $content2 = 'لقد قمت بإرسال رقم هاتفك لاكثر من 10 مرات سوف يتم حظرك';
        $content3 = 'لقد تم بإرسال رقم هاتفك لاكثر من 15 مرات لقد يتم حظرك';
        
        
        
        
        
        // if(!$this->checkPhoneMessagesToday($user,15))
        // {
        //     $this->sendNotificationCheck($title,$content3,$user);
        //     $user->update(['block' => 1]);
        //     Auth::logout();
        // }
        // elseif(!$this->checkPhoneMessagesToday($user,10))
        // {
        //     $this->sendNotificationCheck($title,$content2,$user);
            
        // }
        // elseif(!$this->checkPhoneMessagesToday($user,5))
        // {
        //     $this->sendNotificationCheck($title,$content1,$user);
        // }
        
        $users_data = UserResource::collection($users);
        $data = [
            'message_counter' => $user->chatRoomsCount(),
            'users_data' => $users_data,
            ];
        return $this->returnData('data',$data);
    }

    public function getAllUsersHome()
    {
        $user = User::find(auth()->user()->id);
        $type = ($user->type == 'زوج') ? 'زوجه' : 'زوج';
        $users = User::where('type',$type)->where('is_active',1)
                        ->where('is_showprofile',1)
                        ->where('block',0)
                        ->where('is_active',1)
                        ->where('is_removed',0)
                        ->latest()
                        ->limit(12)
                        ->get();
        $user->update(['last_seen' => \Carbon\Carbon::now(),'is_online' => 1]);
         User::where('is_online', 1)
        ->where('updated_at', '<', now()->subMinutes(3))
        ->update(['is_online' => 0]);
        // if($user->block == 1)
        // {
        //     $user->logout();
        // }
        $title = "تحذير";
        $content1 = 'لقد قمت بإرسال رقم هاتفك لاكثر من 5 مرات سوف يتم حظرك';
        $content2 = 'لقد قمت بإرسال رقم هاتفك لاكثر من 10 مرات سوف يتم حظرك';
        $content3 = 'لقد قمت بإرسال رقم هاتفك لاكثر من 15 مرات لقد يتم حظرك';
        
        
        
        
        
        // if(!$this->checkPhoneMessagesToday($user,15))
        // {
        //     $this->sendNotificationCheck($title,$content3,$user);
        //     $user->update(['block' => 1]);
        //     Auth::logout();
        // }
        // elseif(!$this->checkPhoneMessagesToday($user,10))
        // {
        //     $this->sendNotificationCheck($title,$content2,$user);
            
        // }
        // elseif(!$this->checkPhoneMessagesToday($user,5))
        // {
        //     $this->sendNotificationCheck($title,$content1,$user);
        // }
        
        $users_data = UserResource::collection($users);
        $data = [
            'message_counter' => $user->chatRoomsCount(),
            'users_data' => $users_data,
            ];
        return $this->returnData('data',$data);
    }

    public function getAllUsersNotAut()
    {
        $users = User::where('is_active',1)
                        ->where('type','!=','خاطبه')
                        ->where('is_showprofile',1)
                        ->where('block',0)
                        ->where('is_removed',0)
                        ->latest()
                        ->limit(10)
                        ->get();
        $users_data = UserResource::collection($users);
        return $this->returnData('data',$users_data);
    }
    
    public function checkPhoneMessagesToday($user,$num)
    {
        // Regular expression to detect phone numbers (6 or more digits)
        $phoneRegex = '/\d{6,}/';
    
        // Get the current date
        $today = now()->format('Y-m-d');
    
        // Get all previous messages of the user that contain a phone number sent today
        $messagesToday = ChatRoomMessage::where('user_id', $user->id)
            ->whereDate('created_at', $today) // Filter by today's date
            ->get()
            ->filter(function ($message) use ($phoneRegex) {
                return preg_match($phoneRegex, $message->content);
            });
    
        // Count how many messages with phone numbers have been sent today
        $phoneMessageCountToday = $messagesToday->count();
    
        // Check if the user has sent more than 5 messages with phone numbers today
        if ($phoneMessageCountToday >= $num) {
            return false; // User has exceeded the limit for today
        }
    
        // If the user hasn't exceeded the limit, return true
        return true; // User is allowed to send the message
    }
    
    public function sendNotificationCheck($title,$description,$user)
    {
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
                "token" => $user->fcm,
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
    }

    public function getOneUserSite($id)
    {
        $user = User::find($id);
        if($user)
        {
            $user_data = new OneUserResource($user);
            return $this->returnData('data',$user_data);
        }
        else
        {
            return $this->returnError('',__('site.User_Not_Found'));
        }
    }

    public function search(Request $request)
    {
        $olduser = User::find(auth()->user()->id);
        if($olduser->type == 'خاطبه')
        {
            $users = User::where('parent_id',$olduser->id)
            ->where('is_active',1)
            ->where('block',0)
            ->where('is_removed',0)
            ->when($request->has('name'), function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->input('name') . '%');
            })
            ->when($request->has('nickname'), function ($query) use ($request) {
                $query->where('nickname', 'like', '%' . $request->input('nickname') . '%');
            })
            ->when($request->has('familysitiation'), function ($query) use ($request) {
                $query->where('familysitiation', 'like', '%' . $request->input('familysitiation') . '%');
            })
//            ->when($request->has('min_age') && $request->has('max_age'), function ($query) use ($request) {
//                $query->whereBetween('age', [$request->input('min_age'), $request->input('max_age')]);
//            })
            ->when($request->has('min_age') && $request->has('max_age'), function ($query) use ($request) {
                $minAge = $request->input('min_age');
                $maxAge = $request->input('max_age');

                // Adjust the condition to include the specified age range
                $query->where(function ($subQuery) use ($minAge, $maxAge) {
                    $subQuery->whereBetween('age', [$minAge, $maxAge])
                        ->orWhere('age', $minAge)
                        ->orWhere('age', $maxAge);
                });
            })
            ->when($request->has('nationality'), function ($query) use ($request) {
                $query->whereHas('location', function ($subQuery) use ($request) {
                    $subQuery->where('nationality', $request->input('nationality'));
                });
            })
            ->when($request->has('country'), function ($query) use ($request) {
                $query->whereHas('location', function ($subQuery) use ($request) {
                    $subQuery->where('country', $request->input('country'));
                });
            })
            ->when($request->has('length'), function ($query) use ($request) {
                $query->whereHas('personalInformation', function ($subQuery) use ($request) {
                    $subQuery->where('length', $request->input('length'));
                });
            })
            ->when($request->has('weight'), function ($query) use ($request) {
                $query->whereHas('personalInformation', function ($subQuery) use ($request) {
                    $subQuery->where('weight', $request->input('weight'));
                });
            })
            ->when($request->has('skin_colour'), function ($query) use ($request) {
                $query->whereHas('personalInformation', function ($subQuery) use ($request) {
                    $subQuery->where('skin_colour', $request->input('skin_colour'));
                });
            })
            ->get();
        }
        else
        {
            $users = User::where('type','!=',$olduser->type)
                ->where('is_active',1)
                ->where('block',0)
                ->where('is_removed',0)
                ->when($request->has('name'), function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->input('name') . '%');
                })
                ->when($request->has('nickname'), function ($query) use ($request) {
                    $query->where('nickname', 'like', '%' . $request->input('nickname') . '%');
                })
                ->when($request->has('familysitiation'), function ($query) use ($request) {
                    $query->where('familysitiation', 'like', '%' . $request->input('familysitiation') . '%');
                })
    //            ->when($request->has('min_age') && $request->has('max_age'), function ($query) use ($request) {
    //                $query->whereBetween('age', [$request->input('min_age'), $request->input('max_age')]);
    //            })
                ->when($request->has('min_age') && $request->has('max_age'), function ($query) use ($request) {
                    $minAge = $request->input('min_age');
                    $maxAge = $request->input('max_age');

                    // Adjust the condition to include the specified age range
                    $query->where(function ($subQuery) use ($minAge, $maxAge) {
                        $subQuery->whereBetween('age', [$minAge, $maxAge])
                            ->orWhere('age', $minAge)
                            ->orWhere('age', $maxAge);
                    });
                })
                ->when($request->has('nationality'), function ($query) use ($request) {
                    $query->whereHas('location', function ($subQuery) use ($request) {
                        $subQuery->where('nationality', $request->input('nationality'));
                    });
                })
                ->when($request->has('country'), function ($query) use ($request) {
                    $query->whereHas('location', function ($subQuery) use ($request) {
                        $subQuery->where('country', $request->input('country'));
                    });
                })
                ->when($request->has('length'), function ($query) use ($request) {
                    $query->whereHas('personalInformation', function ($subQuery) use ($request) {
                        $subQuery->where('length', $request->input('length'));
                    });
                })
                ->when($request->has('weight'), function ($query) use ($request) {
                    $query->whereHas('personalInformation', function ($subQuery) use ($request) {
                        $subQuery->where('weight', $request->input('weight'));
                    });
                })
                ->when($request->has('skin_colour'), function ($query) use ($request) {
                    $query->whereHas('personalInformation', function ($subQuery) use ($request) {
                        $subQuery->where('skin_colour', $request->input('skin_colour'));
                    });
                })
                ->get();
        }


        $users_data = UserResource::collection($users);
        return $this->returnData('data',$users_data);
    }
}
