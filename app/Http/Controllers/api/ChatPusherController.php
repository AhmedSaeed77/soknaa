<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Models\ChatRoomMember;
use App\Models\User;
use App\Models\Order;
use App\Models\ChatRoomMessage;
use App\Models\ChatRoom;
use Illuminate\Http\Request;
use App\Http\Resources\api\ChatProvideResource;
use App\Http\Resources\api\ChatMessageResource;
use App\Http\Resources\api\ChatRoomResource;
use App\Traits\GeneralTrait;
use DateTime;
use DateTimeZone;
use App\Events\PushChatMessageEvent;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Traits\Responser;
use Illuminate\Validation\Rule;
use Google\Client as GoogleClient;

use Illuminate\Support\Facades\Http;


class ChatPusherController extends Controller
{

    use Responser;
    use GeneralTrait;

    private $file = [
                        'TEXT' =>
                                    [
                                        'content' => ['required', 'string'],
                                    ],
                    ];

    public function __construct()
    {
        // $this->middleware('auth:api');
    }

    ///////////////////////////////////////////////////////////////////////

    private function roomProvider($user_id, $order_id)
    {
        return ChatRoom::where('order_id', $order_id)
            // ->whereHas('members', function ($query) {
            //     $query->where('user_id', auth()->user()->id);
            // })
            // ->whereHas('members', function ($query) use ($user_id) {
            //     $query->where('user_id', $user_id);
            // })
            ->with('messages');
    }

    public function provideModel($first_user, $order_id, $status = 'OPEN')
    {
        if ($this->roomProvider($first_user, $order_id)->exists())
        {
            return $this->roomProvider($first_user, $order_id)->first();
        }
        else
        {
            $room = ChatRoom::create(['order_id' => $order_id, 'status' => $status]);
            $room->members()->insert([
                                        [
                                            'chat_room_id' => $room->id,
                                            'user_id' => $first_user,
                                            'unread_count' => 0
                                        ],
                                        [
                                            'chat_room_id' => $room->id,
                                            'user_id' => auth()->user()->id,
                                            'unread_count' => 0
                                        ],
                                    ]);


            return $room;
        }
    }

    public function getRoomsModel()
    {
        return ChatRoom::whereHas('members', function ($query) {
                $query->where('user_id', auth()->user()->id);
            })
            ->orderByDesc('updated_at')
            ->get();
    }

    public function resetUnread($room_id)
    {
        return ChatRoomMember::where('chat_room_id', $room_id)->where('user_id', auth()->user()->id)->update(['unread_count' => 0]);
    }

    ///////////////////////////////////////////////////////////////////////

    public function provide(Request $request)
    {
        $request->validate([
                                'user_id' => ['required', Rule::exists('users', 'id')],
                                'order_id' => ['nullable', Rule::exists('orders', 'id')],
                            ]);
        $order = Order::find($request->order_id);
        if($order)
        {
            $chats = $this->provideModel($request->user_id, $order->id);
            $chats_data = new ChatProvideResource($chats);
            $messages_data = ChatMessageResource::collection($this->getRoomMessages($chats->id));
            $data = [
                        'chats' => $chats_data,
                        'messages' => $messages_data,
                    ];
            return $data;

        }
        else
        {
            $order = Order::create([
                                        'from' => auth()->user()->id,
                                        'to' => $request->user_id,
                                    ]);
            $chats = $this->provideModel($request->user_id , $order->id);
            $chats_data = new ChatProvideResource($chats);

            $messages_data = ChatMessageResource::collection($this->getRoomMessages($chats->id));
            $data = [
                        'chats' => $chats_data,
                        'messages' => $messages_data,
                    ];
            return $data;
        }

    }

    public function getRooms()
    {
        $rooms = $this->getRoomsModel();
        $rooms_data = ChatRoomResource::collection($rooms);
        return $rooms_data;
    }

    /////////////////////////////////////////////////////////////////////////
    public function getRoomMessages($room_id, $after_message_id = null)
    {
        return ChatRoomMessage::where('chat_room_id', $room_id)
            ->where(function ($query) use ($after_message_id) {
                if ($after_message_id !== null)
                    $query->where('id', '<', $after_message_id);
            })
            ->orderByDesc('id')
            ->limit(20)
            ->get()
            ->sortBy('id');
    }
    /////////////////////////////////////////////////////////////////////////

    public function getMessages($room_id)
    {
        $room = ChatRoom::find($room_id);
        $room_member = ChatRoomMember::where('user_id', auth()->user()->id)->where('chat_room_id', $room->id)->first();
        $room_member->update(['unread_count' => 0]);
        return ChatMessageResource::collection($this->getRoomMessages($room_id));
    }

    public function loadMoreMessages(Request $request, $room_id)
    {
        $room = ChatRoom::find($room_id);
        if (Gate::allows('access-room', $room))
        {
            return ChatMessageResource::collection($this->getRoomMessages($room_id,$request->after_message_id));
        }
        else
        {
            return $this->responseCustom(401, __('messages.You are not allowed to access this resource'));
        }
    }

    public function send(Request $request, $room_id)
    {
        $request->validate([
                                // 'user_id' => ['required'],
                                // 'second_user' => ['required'],
                                'type' => ['required', Rule::in(['TEXT', 'IMAGE', 'AUDIO', 'FILE'])],
                                ...$this->file[$request->type],
                            ]);

        $room = ChatRoom::find($room_id);
        if($room->status == 'OPEN')
        {
            if($room->members->count() != 0)
            {
                foreach($room->members as $member)
                {
                    // return $member->user;
                    if($member->user->id == auth()->user()->id)
                    continue;
                    if($member->user->is_removed == 1)
                    {
                        return $this->responseCustom(202,'لا يمكن التواصل مع هذا العضو ف الوقت الحالي');
                    }
                }
            }
            
            // DB::beginTransaction();
            try
            {
                $data = $request->input();
                if ($request->type != 'TEXT')
                {
                    $data['content'] = $this->uploadNotTextMessage($request->file);
                    unset($data['file']);
                }
                $newuser = User::find(auth()->user()->id);
                // $checkPhone = $this->checkPhone($data['content'],$newuser);
                // if($checkPhone == false)
                // {
                //     return $this->responseFail(message: __('dashboard.Your_phone_is_repeat'));
                // }
                $chatRoom = ChatRoom::find($room_id);
                $userIds = $chatRoom->members()
                                    ->where('user_id', '!=', auth()->id())
                                    ->pluck('user_id')
                                    ->toArray();
                                    
                $fcmTokens = User::whereIn('id', $userIds)
                                ->get();
                                // return $fcmTokens;
                $title = "تحذير";
                $content1 = 'لقد قمت بإرسال رقم هاتفك لاكثر من 5 مرات سوف يتم حظرك';
                $content2 = 'لقد قمت بإرسال رقم هاتفك لاكثر من 10 مرات سوف يتم حظرك';
                $content3 = 'لقد تم بإرسال رقم هاتفك لاكثر من 15 مرات لقد يتم حظرك';
                
                $fcmToken = $fcmTokens->first();
                // return $fcmToken;
                
                
                if(!$this->checkPhoneMessagesToday($fcmToken,16))
                {
                    $newuser->update(['block' => 1]);
                    \Log::info($newuser);
                    $this->sendNotificationCheck($title,$content3,$newuser);
                    Auth::logout();
                    return $this->responseFail('401','لقد تم حظرك');
                }
                elseif(!$this->checkPhoneMessagesToday($fcmToken,11))
                {
                    $this->sendNotificationCheck($title,$content2,$newuser);
                    
                }
                elseif(!$this->checkPhoneMessagesToday($fcmToken,6))
                {
                    $this->sendNotificationCheck($title,$content1,$newuser);
                }
                
                $timeZoneName = $request->input('time_zone_name');
                $serverTime = new DateTime('now');
                $userTimeZone = new DateTimeZone($timeZoneName);
                $serverTime->setTimezone($userTimeZone);
                $userDateTime = $serverTime->format('Y-m-d H:i:s');
                $message = ChatRoomMessage::create([
                                                        'chat_room_id' => $room_id,
                                                        'user_id' => auth()->user()->id,
                                                        'content' => $data['content'],
                                                        'type' => $data['type'],
                                                        'sent_at' => $userDateTime,
                'time_zone' => $timeZoneName,
                                                    ]);

                $room->update(['updated_at' => Carbon::now()]);

                $chatroommemeber = ChatRoomMember::where('chat_room_id', $room_id)
                                                    ->where('user_id', '!=', auth()->user()->id)
                                                    ->increment('unread_count');

                broadcast(new PushChatMessageEvent($message))->toOthers();
                
                
                
                // $devicetokens = ['cG-BHhQUT--hrMo1I7_H9m:APA91bGroIJLf_fT3VAEhnlUd83cknBa7L033Cwl6BDGSd9YeaZAHGh8rxdkRINJI7vZD0mRgOHoO8KC1CM48-qkFOYsttEdCV4kJ0dp2Rau6F6oFuZKBDisGCG4ATb3bJx2uKzFqPvi'];
                
                $title = "رسالة جديدة";
                $content = $data['content'];
                $fcmToken = $fcmTokens->first();
                $this->sendfirbase($fcmToken,$title,$content);
                // $this->notify($fcmTokens,$title,$content);
                // DB::commit();
                $data = new ChatMessageResource($message);
                return $data;
            }
            catch (Exception $e)
            {
                // DB::rollBack();
                \Log::warning('send chat error: ' . $e);
                return $this->responseFail(message: __('messages.Something went wrong'));
            }
        }
        else
        {
            if($room->block_from == auth()->user()->id)
            {
                return $this->responseCustom(202, __('dashboard.you_have_blocked_this_member'));
            }
            else
            {
                return $this->responseCustom(202, __('dashboard.this_user_have_blocked_you'));
            }
        }
    }
    
    //////////////////////////////////////////////////////////////
    
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
    
    public function checkPhoneMessagesToday($user,$num)
    {
        // Regular expression to detect phone numbers (6 or more digits)
        $phoneRegex = '/\d{6,}/';
    
        // Get the current date
        $today = now()->format('Y-m-d');
    
        // Get all previous messages of the user that contain a phone number sent today
        $messagesToday = ChatRoomMessage::where('user_id', auth()->user()->id)
            ->whereDate('created_at', $today) // Filter by today's date
            ->get()
            ->filter(function ($message) use ($phoneRegex) {
                return preg_match($phoneRegex, $message->content);
            });
    
        // Count how many messages with phone numbers have been sent today
        $phoneMessageCountToday = $messagesToday->count();
    
        // Check if the user has sent more than 5 messages with phone numbers today
        if ($phoneMessageCountToday == $num) {
            return false; // User has exceeded the limit for today
        }
    
        // If the user hasn't exceeded the limit, return true
        return true; // User is allowed to send the message
    }
    //////////////////////////////////////////////////////////////
    
    /////////////////////////////////////////////////////////////
    public function checkPhone($message, $user)
    {
        // Regular expression to detect phone numbers (6 or more digits)
        $phoneRegex = '/\d{6,}/';
    
        // Check if the current message contains a phone number
        if (preg_match($phoneRegex, $message)) {
            // Get all previous messages of the user
            $previousMessages = ChatRoomMessage::where('user_id', $user->id)->get();
    
            $phoneNumberCount = 0;
    
            // Count previous messages that contain a phone number
            foreach ($previousMessages as $previousMessage) {
                if (preg_match($phoneRegex, $previousMessage->content)) {
                    $phoneNumberCount++;
                }
            }
    
            // If user has already sent more than 5 messages with phone numbers, prevent sending
            if ($phoneNumberCount >= 5) {
                return false; // Do not allow the message to be sent
            }
        }
    
        // If the message doesn't contain a phone number, allow it to be sent
        return true;
    }
    ////////////////////////////////////////////////////////////
    public function sendfirbase($fcm,$title,$description)
    {
        

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
            "token" => $fcm->fcm,
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
    
    public function notify($deviceTokens,$title,$content)
    {
        $notification = $this->notificationScheme($deviceTokens,$title,$content);
        $serverApiKey = 'AAAA5TQDlA8:APA91bE6PDdJigtCOwjLW9eTxZ4aOZNlBNo9GEbrle3zH6i5E8V8O5av3fZVEv_YvZSSvkhSggelHPR5qmCYzIdhxdEEqV_ftLz9_EicHprFKCufQJPcC4HTgM31VmjAr6yMD69xqBAt';
\Log::warning('send chat error: ');
        $headers = [
                        'Authorization: key=' .$serverApiKey,
                        'Content-Type: application/json',
                    ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $notification);
        curl_exec($ch);
    }

    private function notificationScheme(array $deviceTokens, string $title, string $content)
    {
        return json_encode([
                                'registration_ids'  => $deviceTokens,
                                'notification'      => [
                                                            'title' => $title,
                                                            'body' => $content,
                                                        ],
                            ]);
    }


    public function uploadNotTextMessage($type)
    {
        $path = null;
        if ($type)
        {
            $path = 'storage/' . request()->file('file')->store('message', 'public');
        }
        return $path;
    }

    public function read($room_id)
    {
        $room = ChatRoom::find($room_id);
        if (Gate::allows('access-room', $room))
        {
            $this->resetUnread($room_id);
            //$this->fireRoomEvent($room);
            return $this->responseSuccess();
        }
        else
        {
            return $this->responseCustom(401, __('messages.You are not allowed to access this resource'));
        }
    }

    private function fireRoomEvent($room)
    {
        $parties = $this->chatRoomMemberRepository->get('chat_room_id', $room->id);

        foreach ($parties as $party)
        {
            broadcast(new ChatRoomEvent($room, $party->user?->id));
        }
    }

    public function goOnline()
    {
        $this->userRepository->update(auth()->user()->id, ['is_online' => true]);

        broadcast(new OnlineStateEvent(auth()->user(), auth()->user()->id));

        return $this->responseSuccess();
    }

    public function goOffline()
    {
        $this->userRepository->update(auth()->user()->id, ['is_online' => false]);

        broadcast(new OnlineStateEvent(auth()->user(), auth()->user()->id));

        return $this->responseSuccess();
    }

    public function deleteRoome($id)
    {
        $room = ChatRoom::find($id);
        $room->delete();

        return $this->returnData('data',__('dashboard.recored deleted successfully.'),__('dashboard.recored deleted successfully.'));
    }

    public function blockRoom($id)
    {
        $room = ChatRoom::find($id);
        $room->update(['status' => 'CLOSE' , 'block_from' => auth()->user()->id]);
        $room->save();
        return $this->returnData('data',__('dashboard.recored blocked successfully.'),__('dashboard.recored blocked successfully.'));
    }


}
