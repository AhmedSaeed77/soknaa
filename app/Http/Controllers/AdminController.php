<?php

namespace App\Http\Controllers;
use App\Http\Requests\AdminLoginRequest;
use Illuminate\Http\Request;
use App\Traits\GeneralTrait;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

class AdminController extends Controller
{
    use GeneralTrait;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function login(AdminLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $token = Auth::guard('admin')->attempt($credentials);
        if($token)
        {
            $admin = Admin::where('email',$request->email)->first();
            if (\auth('admin')->user()->status == 0)
            {
                return $this->returnError(422,__('dashboard.admin_not_active'));
            }
                
            return $this->returnData('data',['admin_data' => $admin , 'token' => $token] , __('dashboard.admin_Is_Login'));
        }
        return $this->returnError(422,__('dashboard.admin_Not_Found'));
    }

    public function logout()
    {

    }
}
