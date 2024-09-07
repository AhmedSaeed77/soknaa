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

                    if($member->user->is_removed == 1)
                    {
                        return $this->responseCustom(401,'لا يمكن التواصل مع هذا العضو ف الوقت الحالي');
                    }
                }
            }
            {

            }
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
        else
        {
            if($room->block_from == auth()->user()->id)
            {
                return $this->responseCustom(401, __('dashboard.you_have_blocked_this_member'));
            }
            else
            {
                return $this->responseCustom(401, __('dashboard.this_user_have_blocked_you'));
            }
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
