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

    public function getRequestsToJoin(Request $request)
    {
        $users = User::where('is_active',0)
                        ->when($request->search, function ($query) use ($request) {
                            return $query->where('name', 'like', '%' . $request->search . '%');
                        })
                        ->when($request->search, function ($query) use ($request) {
                            return $query->where('nickname', 'like', '%' . $request->search . '%');
                        })
                        ->when($request->gender, function ($query) use ($request) {
                            return $query->where('sex', 'like', '%' . $request->gender . '%');
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
        $users_data = UserResource::collection($users)->response()->getData(true);
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
                    return $this->returnData('data',__('site.request_accept'), __('site.request_accept'));
                }
                return $this->returnData('data',__('site.request_reject'), __('site.request_reject'));
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

    public function getAllMembers(Request $request)
    {
        try
        {
            $users = User::where('is_active',1)->
                            when($request->gender, function ($query) use ($request) {
                                return $query->where('sex', 'like', '%' . $request->gender . '%');
                            })
                            ->when($request->type, function ($query) use ($request) {
                                return $query->where(function ($query) use ($request) {
                                    if ($request->type == 'خاطبه') {
                                        $query->where('type', '=', 'خاطبه');
                                    } elseif ($request->type == 'عادى') {
                                        $query->where('type', 'like', '%زوج%')
                                            ->orWhere('type', 'like', '%زوجه%');
                                    }
                                });
                            })
                            ->when($request->name, function ($query) use ($request) {
                                return $query->where('name', 'like', '%' . $request->name . '%');
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
            $users_data = UserResource::collection($users)->response()->getData(true);
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
                if($user->block == 1)
                {
                    $user->update(['block' => 0]);
                    return $this->returnData('data',__('site.profile_active'), __('site.profile_active'));
                }
                else
                {
                    $user->update(['block' => 1]);
                    return $this->returnData('data',__('site.profile_blocked'), __('site.profile_blocked'));
                }
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
                if($user->type == 'خاطبه')
                {
                    $user->children()->delete();
                    $user->delete();
                    return $this->returnData('data',__('dashboard.item_is_deleted'),__('dashboard.item_is_deleted'));
                }
                else
                {
                    $user->delete();
                    return $this->returnData('data',__('dashboard.item_is_deleted'),__('dashboard.item_is_deleted'));
                }
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
