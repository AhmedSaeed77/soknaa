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

    public function reset(UserResetRequest $request)
    {
        try
        {
            $user = User::where('email',$request->email)->first();
            if($user)
            {
                $randomNumber = random_int(1000, 9999);
                $details = [
                                'title' => 'Reset',
                                'body' =>  $randomNumber,
                            ];

                Mail::to($request->email)->send(new NotifyMail($details));
                $this->resetpasswordRepository->deleteItem('user_id',$user->id);
                $this->resetpasswordRepository->create(['user_id' => $user->id, 'reset' => $randomNumber]);
                return $this->returnData('data',__('site.Email_Send'), __('site.Email_Send'));
            }
            else
            {
                return $this->returnError('',__('site.Email_Not_Found'));
            }
        }
        catch (\Exception $e)
        {
            return response()->json($e->getMessage(), 401);
        }
    }

    public function resetUserconfirm(UserConfirmRequest $request)
    {
        try
        {
            $reset = $this->resetpasswordRepository->checkItem('reset',$request->confirm);
            if($reset)
            {
                return $this->returnData('data',__('site.code_Is_Confirm'), __('site.code_Is_Confirm'));
            }
            else
            {
                return $this->returnError('',__('site.code_Not_Confirm'));
            }
        }
        catch (\Exception  $e)
        {
            return response()->json($e->getMessage(), 401);
        }
    }

    public function changePassword(UserChangePasswordDashboardRequest $request)
    {
        try
        {
            $user = $this->userRepository->checkItem('email',$request->email);
            if($user)
            {
                $this->userRepository->update($user->id,['password' => Hash::make($request->newpassword)]);
                $this->resetpasswordRepository->deleteItem('user_id',$user->id);
                return $this->returnData('data',__('site.password_Is_Changed'));
            }
            return $this->returnError('',__('site.User_Not_Found'));
        }
        catch (\Exception  $e)
        {
            return response()->json($e->getMessage(), 401);
        }
    }
}
