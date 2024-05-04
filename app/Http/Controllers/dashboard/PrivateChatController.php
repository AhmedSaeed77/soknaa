<?php

namespace App\Http\Controllers\dashboard;
use App\Models\PrivatChat;
use App\Models\User;
use App\Models\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\dashboard\FromMesageResource;
use App\Http\Resources\dashboard\ToMessageResource;
use App\Http\Resources\dashboard\PrivateChatResource;
use Illuminate\Support\Facades\DB;

use App\Traits\GeneralTrait;

class PrivateChatController extends Controller
{
    use GeneralTrait;

    public function getAllPrivateCaht(Request $request)
    {
        $allchats = User::when($request->name, function ($query) use ($request) {
                    return $query->where('name', 'like', '%' . $request->name . '%');
                })
                ->leftJoin('privat_chats as pc1', 'users.id', '=', 'pc1.from_user')
                ->leftJoin('privat_chats as pc2', 'users.id', '=', 'pc2.to_user')
                ->select('users.id', 'users.name','users.nickname','users.membership_num','users.email','users.phone', DB::raw('COUNT(pc1.id) + COUNT(pc2.id) as private_chats_count'))
                ->groupBy('users.id', 'users.name','users.nickname','users.membership_num','users.email','users.phone')
                ->orderBy('private_chats_count', 'desc')
                ->paginate(15);

        $allchats_data = PrivateChatResource::collection($allchats)->response()->getData(true);
        return $this->returnData('data', $allchats_data);
    }

    public function createPrivateMessagesForUser(Request $request)
    {
        $request->validate([
                                'message' => 'required',
                                'to_user' => 'required',
                                'type' => 'required|in:0,1',
                            ]

                        );
        try
        {
            if($request->type == 0)
            {
                 PrivatChat::create([
                                    'to_user' => $request->to_user , 
                                    'from_admin' => auth()->user()->id , 
                                    'message' => $request->message,
                                    'type' => $request->type
                                ]);
            }
            else
            {
                PrivatChat::create([
                                        'to_user' => $request->to_user , 
                                        'from_admin' => auth()->user()->id , 
                                        'message' =>  $this->handle('message', 'privatechat'),
                                        'type' => $request->type
                                ]);
            }
           
            // $user = User::find($request->to_user);
            $devicetokens = User::where('id',$request->to_user)->pluck('fcm')->toArray();
            $title = "رساله جديده";
            $this->notify($devicetokens,$title,$request->message);

            return $this->returnData('data',__('dashboard.item_is_added'),__('dashboard.item_is_added'));
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function notify($deviceTokens,$title,$content)
    {
        $notification = $this->notificationScheme($deviceTokens,$title,$content);
        $serverApiKey = 'AAAA5TQDlA8:APA91bE6PDdJigtCOwjLW9eTxZ4aOZNlBNo9GEbrle3zH6i5E8V8O5av3fZVEv_YvZSSvkhSggelHPR5qmCYzIdhxdEEqV_ftLz9_EicHprFKCufQJPcC4HTgM31VmjAr6yMD69xqBAt';
        
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

    // private function preparePush($deviceTokens,$title,$content)
    // {
    //     return $this->notificationScheme(deviceTokens: $deviceTokens, title: $title, content: $content);
    // }

    public function getAllPrivateMessagesForUser($id)
    {
        $fromMessages = PrivatChat::where('from_user', $id)->get();
        $toMessages = PrivatChat::where('to_user', $id)->get();

        $allMessages = collect([]);

        if (!$fromMessages->isEmpty())
        {
            $allMessages = $allMessages->merge($fromMessages->map(function ($message) {
                return ['type' => 0, 'message' => $message];
            }));
        }

        if (!$toMessages->isEmpty())
        {
            $allMessages = $allMessages->merge($toMessages->map(function ($message) {
                return ['type' => 1, 'message' => $message];
            }));
        }

        if (!$allMessages->isEmpty())
        {
            $allMessages = $allMessages->sortBy(function ($item) {
                return $item['message']->created_at;
            });
        }
        // \Log::info($allMessages);
        $allMessages = $allMessages->values();
        $allMessages = FromMesageResource::collection($allMessages);
        return $this->returnData('data', $allMessages);
    }
  
}
