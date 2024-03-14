<?php

namespace App\Http\Controllers\api;
use App\Models\Chat;
use App\Models\User;
use App\Models\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\dashboard\FromMesageResource;
use App\Http\Resources\dashboard\ToMessageResource;

use App\Traits\GeneralTrait;

class ChatMobileController extends Controller
{
    use GeneralTrait;

    public function createmessage(Request $request)
    {
        $request->validate([
                                'message' => 'required',
                                // 'from_user' => 'required',
                            ]
                        );
        try
        {
            Chat::create([
                            'to_admin' => Admin::first()->id , 
                            'from_user' => auth()->user()->id , 
                            'message' => $request->message
                        ]);
            return $this->returnData('data',__('dashboard.item_is_added'),__('dashboard.item_is_added'));
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getAllMessagesForUser()
    {
        $fromMessages = Chat::where('from_user', auth()->user()->id)->get();
        $toMessages = Chat::where('to_user', auth()->user()->id)->get();

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
