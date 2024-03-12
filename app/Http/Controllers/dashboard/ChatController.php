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
        $frommessages = Chat::where('from_user',$id)->orderBy('created_at', 'desc')->get();
        $tomessages = Chat::where('to_user',$id)->orderBy('created_at', 'desc')->get();

        $frommessages_data = FromMesageResource::collection($frommessages);
        $tomessages_data = ToMessageResource::collection($tomessages);
        
        $data = [
                    'frommessages_data' => $frommessages_data,
                    'tomessages_data' => $tomessages_data,
                ];

        return $this->returnData('data',$data);
    }

}
