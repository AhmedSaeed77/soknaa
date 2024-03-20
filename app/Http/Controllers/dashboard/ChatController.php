<?php

namespace App\Http\Controllers\dashboard;
use App\Models\Chat;
use App\Models\User;
use App\Models\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\dashboard\FromMesageResource;
use App\Http\Resources\dashboard\ToMessageResource;

use App\Traits\GeneralTrait;

class ChatController extends Controller
{
    use GeneralTrait;

    public function create(Request $request)
    {
        $request->validate([
                                'message' => 'required',
                                'to_user' => 'required',
                            ]

                        );
        try
        {
            Chat::create([
                            'to_user' => $request->to_user , 
                            'from_admin' => auth()->user()->id , 
                            'message' => $request->message
                        ]);
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

    public function getAllMessagesForUser($id)
    {
        $fromMessages = Chat::where('from_user', $id)->get();
        $toMessages = Chat::where('to_user', $id)->get();

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
