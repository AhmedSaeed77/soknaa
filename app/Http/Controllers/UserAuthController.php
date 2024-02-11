<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Http\Requests\api\UserLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\GeneralTrait;

class UserAuthController extends Controller
{
    use GeneralTrait;

    public function login(UserLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $token = Auth::guard('web')->attempt($credentials);
        if($token)
        {
            $user = User::where('email',$request->email)->first();
            // if (\auth('web')->user()->status == 0)
            // {
            //     return $this->returnError(422,__('dashboard.admin_not_active'));
            // }
                
            return $this->returnData('data',['user_data' => $user , 'token' => $token] , __('dashboard.admin_Is_Login'));
        }
        return $this->returnError(422,__('dashboard.Incorrect email or password'));
    }
}
