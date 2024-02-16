<?php

namespace App\Http\Controllers\dashboard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\GeneralTrait;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\dashboard\AcceptRejectRequest;
use App\Http\Resources\dashboard\UserResource;
use App\Http\Resources\dashboard\OneUserResource;

class UserController extends Controller
{
    use GeneralTrait;

    public function getRequestsToJoin()
    {
        $users = User::where('is_active',0)->get();
        $users_data = UserResource::collection($users);
        return $this->returnData('data',$users_data);
    }

    public function getOneRequestToJoin($id)
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

    public function acceptReject(AcceptRejectRequest $request)
    {
        try
        {
            $user = User::find($request->user_id);
            if($user)
            {
                $user->update(['status' => $request->accept]);
                if($request->accept == 1)
                {
                    $user->update(['is_active' => 1]);
                }
                return $this->returnData('data',__('site.User_Profile_Updated'), __('site.User_Profile_Updated'));
            }
            else
            {
                return $this->returnError('',__('site.User_Not_Found'));
            }
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getAllMembers()
    {
        try
        {
            $users = User::where('status',1)->where('is_active',1)->get();
            $users_data = UserResource::collection($users);
            return $this->returnData('data',$users_data);
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getOneMember($id)
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

    public function block($id)
    {
        try
        {
            $user = User::find($id);
            if($user)
            {
                $user->update(['block' => 1]);
                return $this->returnData('data',__('site.User_Profile_Updated'), __('site.User_Profile_Updated'));
            }
            else
            {
                return $this->returnError('',__('site.User_Not_Found'));
            }
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteMember($id)
    {
        try
        {
            $user = User::find($id);
            if($user)
            {
                $user->delete();
                return $this->returnData('data',__('dashboard.item_is_deleted'),__('dashboard.item_is_deleted'));
            }
            else
            {
                return $this->returnError('',__('site.User_Not_Found'));
            }
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
