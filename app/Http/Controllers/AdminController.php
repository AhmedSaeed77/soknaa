<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Requests\dashboard\AdminLoginRequest;
use App\Http\Requests\dashboard\AdminRequest;
use Illuminate\Http\Request;
use App\Traits\GeneralTrait;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\dashboard\AdminResource;

class AdminController extends Controller
{
    use GeneralTrait;

    public function __construct()
    {
        // $this->middleware('guest')->except('logout');
        $this->middleware('auth:admin')->only('logout');
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
        return $this->returnError(422,__('dashboard.Incorrect email or password'));
    }

    public function logout()
    {

    }

    public function index(Request $request)
    {
        $admins = Admin::when($request->name, function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->name . '%');
        })
        ->when($request->role, function ($query) use ($request) {
            return $query->where('role', 'like', '%' . $request->role . '%');
        })
        ->when($request->date == 1, function ($query) {
            return $query->whereDate('created_at', now()->toDateString());
        })
        ->when($request->date == 2, function ($query) {
            $startOfWeek = now()->startOfWeek();
            $endOfWeek = now()->endOfWeek();
            return $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
        })
        ->when($request->date == 3, function ($query) {
            return $query->whereMonth('created_at', now()->month);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(15);
        $admins_data = AdminResource::collection($admins)->response()->getData(true);;
        return $this->returnData('data',$admins_data);
    }

    public function store(AdminRequest $request)
    {
        try
        {
            Admin::create([
                                'name' => $request->name,
                                'email' => $request->email,
                                'password' => Hash::make($request->password),
                                'phone' => $request->phone,
                                'role' => $request->role,
                            ]);
            return $this->returnData('data',__('dashboard.item_is_added'),__('dashboard.item_is_added'));
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $admin = Admin::find($id);
        $admin_data = new AdminResource($admin);
        return $this->returnData('data',$admin_data);
    }

    public function update(AdminRequest $request,$id)
    {
        try
        {
            $data = $request->only('name', 'email', 'phone', 'role', 'password');
            if($request->password)
            {
                $password = Hash::make($request->password);
                $data = array_merge($data,["password"=>$password]);
            }
            $admin = Admin::find($id);
            $admin->update($data);
            return $this->returnData('data',__('dashboard.item_is_updated'),__('dashboard.item_is_updated'));
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try
        {
            $admin = Admin::find($id);
            $admin->delete();
            return $this->returnData('data',__('dashboard.item_is_deleted'),__('dashboard.item_is_deleted'));
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function block(Request $request,$id)
    {
        try
        {
            if($request->block == 1)
            {
                $admin = Admin::find($id);
                $admin->update(['block' => $request->block]);
                $admin->update(['status' => 0]);
                return $this->returnData('data',__('dashboard.admin_is_blocked'),__('dashboard.admin_is_blocked'));
            }
            else
            {
                $admin = Admin::find($id);
                $admin->update(['block' => $request->block]);
                $admin->update(['status' => 1]);
                return $this->returnData('data',__('dashboard.admin_is_unblocked'),__('dashboard.admin_is_unblocked'));
            }
            
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
