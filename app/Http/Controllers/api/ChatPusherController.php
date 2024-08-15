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

use App\Events\PushChatMessageEvent;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Traits\Responser;
use Illuminate\Validation\Rule;


class ChatPusherController extends Controller
{

    use Responser;

    private $file = [
                        'TEXT' =>
                                    [
                                        'content' => ['required', 'string'],
                                    ],
                    ];

    public function __construct()
    {
        // $this->middleware('auth:api-app');
    }

    ///////////////////////////////////////////////////////////////////////

    private function roomProvider($user_id, $order_id)
    {
        return ChatRoom::where('order_id', $order_id)
            // ->whereHas('members', function ($query) {
            //     $query->where('user_id', $user_id);
            // })
            ->whereHas('members', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->with('messages');
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
                $query->where('user_id', auth('api-app')->id());
            })
            ->orderByDesc('updated_at')
            ->get();
    }

    public function resetUnread($room_id)
    {
        return ChatRoomMember::where('chat_room_id', $room_id)->where('user_id', auth('api-app')->id())->update(['unread_count' => 0]);
    }

    ///////////////////////////////////////////////////////////////////////

    public function provide(Request $request)
    {
        $request->validate([
                                'user_id' => ['required', Rule::exists('users', 'id')],
                                // 'order_id' => ['required', Rule::exists('orders', 'id')],
                                'status' => ['required', 'in:OPEN,CLOSE'],
                                // 'first_user' => ['required'],
                                // 'second_user' => ['required'],
                            ]);
        // $order_id = 43;
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
        if (Gate::allows('access-room', $room))
        {
            return ChatMessageResource::collection($this->getRoomMessages($room_id));
        }
        else
        {
            return $this->responseCustom(401, __('messages.You are not allowed to access this resource'));
        }
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
        DB::beginTransaction();
        try
        {
            $data = $request->input();
            if ($request->type != 'TEXT')
            {
                $data['content'] = $this->uploadNotTextMessage($request->file);
                unset($data['file']);
            }
            $message = ChatRoomMessage::create([
                                                    'chat_room_id' => $room_id,
                                                    'user_id' => auth()->user()->id,
                                                    'content' => $data['content'],
                                                    'type' => $data['type']
                                                ]);

            $room->update(['updated_at' => Carbon::now()]);

            $chatroommemeber = ChatRoomMember::where('chat_room_id', $room_id)
                                                ->where('user_id', '!=', auth()->user()->id)
                                                ->increment('unread_count');

            broadcast(new PushChatMessageEvent($message))->toOthers();
            DB::commit();
            $data = new ChatMessageResource($message);
            return $data;
        }
        catch (Exception $e)
        {
            DB::rollBack();
            Log::warning('send chat error: ' . $e);
            return $this->responseFail(message: __('messages.Something went wrong'));
        }
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
        $this->userRepository->update(auth('api-app')->id(), ['is_online' => true]);

        broadcast(new OnlineStateEvent(auth('api-app')->user(), auth('api-app')->id()));

        return $this->responseSuccess();
    }

    public function goOffline()
    {
        $this->userRepository->update(auth('api-app')->id(), ['is_online' => false]);

        broadcast(new OnlineStateEvent(auth('api-app')->user(), auth('api-app')->id()));

        return $this->responseSuccess();
    }
}
