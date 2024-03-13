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
            return $this->returnData('data',__('dashboard.item_is_added'),__('dashboard.item_is_added'));
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

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
