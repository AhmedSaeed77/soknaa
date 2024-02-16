<?php

namespace App\Http\Controllers\api;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\api\UserResource;
use App\Http\Resources\api\OneUserResource;
use App\Traits\GeneralTrait;

class SiteController extends Controller
{
    use GeneralTrait;

    public function getAllUsers()
    {
        $user = User::find(auth()->user()->id);
        $type = ($user->type == 'زوج') ? 'زوجه' : 'زوج';
        $users = User::where('type',$type)->where('is_active',1)
                        ->where('is_showprofile',1)
                        ->where('block',0)
                        ->get();
        $users_data = UserResource::collection($users);
        return $this->returnData('data',$users_data);
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

    public function search()
    {
        
    }
}
