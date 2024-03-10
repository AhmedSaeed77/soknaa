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
        $users = User::where('type',$type)
                        ->where('is_showprofile',1)
                        ->where('block',0)
                        ->where('is_active',1)
                        ->get();
        $users_data = UserResource::collection($users);
        return $this->returnData('data',$users_data);
    }

    public function getAllUsersHome()
    {
        $user = User::find(auth()->user()->id);
        $type = ($user->type == 'زوج') ? 'زوجه' : 'زوج';
        $users = User::where('type',$type)->where('is_active',1)
                        ->where('is_showprofile',1)
                        ->where('block',0)
                        ->where('is_active',1)
                        ->latest()
                        ->limit(12)
                        ->get();
        $users_data = UserResource::collection($users);
        return $this->returnData('data',$users_data);
    }

    public function getAllUsersNotAut()
    {
        $users = User::where('is_active',1)
                        ->where('is_showprofile',1)
                        ->where('block',0)
                        ->latest()
                        ->limit(10)
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

    public function search(Request $request)
    {
        $olduser = User::find(auth()->user()->id);
        if($olduser->type == 'خاطبه')
        {
            $users = User::where('parent_id',$olduser->id)
            ->where('is_active',1)
            ->where('block',0)
            ->when($request->has('name'), function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->input('name') . '%');
            })
            ->when($request->has('nickname'), function ($query) use ($request) {
                $query->where('nickname', 'like', '%' . $request->input('nickname') . '%');
            })
            ->when($request->has('familysitiation'), function ($query) use ($request) {
                $query->where('familysitiation', 'like', '%' . $request->input('familysitiation') . '%');
            })
//            ->when($request->has('min_age') && $request->has('max_age'), function ($query) use ($request) {
//                $query->whereBetween('age', [$request->input('min_age'), $request->input('max_age')]);
//            })
            ->when($request->has('min_age') && $request->has('max_age'), function ($query) use ($request) {
                $minAge = $request->input('min_age');
                $maxAge = $request->input('max_age');

                // Adjust the condition to include the specified age range
                $query->where(function ($subQuery) use ($minAge, $maxAge) {
                    $subQuery->whereBetween('age', [$minAge, $maxAge])
                        ->orWhere('age', $minAge)
                        ->orWhere('age', $maxAge);
                });
            })
            ->when($request->has('nationality'), function ($query) use ($request) {
                $query->whereHas('location', function ($subQuery) use ($request) {
                    $subQuery->where('nationality', $request->input('nationality'));
                });
            })
            ->when($request->has('country'), function ($query) use ($request) {
                $query->whereHas('location', function ($subQuery) use ($request) {
                    $subQuery->where('country', $request->input('country'));
                });
            })
            ->when($request->has('length'), function ($query) use ($request) {
                $query->whereHas('personalInformation', function ($subQuery) use ($request) {
                    $subQuery->where('length', $request->input('length'));
                });
            })
            ->when($request->has('weight'), function ($query) use ($request) {
                $query->whereHas('personalInformation', function ($subQuery) use ($request) {
                    $subQuery->where('weight', $request->input('weight'));
                });
            })
            ->when($request->has('skin_colour'), function ($query) use ($request) {
                $query->whereHas('personalInformation', function ($subQuery) use ($request) {
                    $subQuery->where('skin_colour', $request->input('skin_colour'));
                });
            })
            ->get();
        }
        else
        {
            $users = User::where('type','!=',$olduser->type)
                ->where('is_active',1)
                ->when($request->has('name'), function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->input('name') . '%');
                })
                ->when($request->has('nickname'), function ($query) use ($request) {
                    $query->where('nickname', 'like', '%' . $request->input('nickname') . '%');
                })
                ->when($request->has('familysitiation'), function ($query) use ($request) {
                    $query->where('familysitiation', 'like', '%' . $request->input('familysitiation') . '%');
                })
    //            ->when($request->has('min_age') && $request->has('max_age'), function ($query) use ($request) {
    //                $query->whereBetween('age', [$request->input('min_age'), $request->input('max_age')]);
    //            })
                ->when($request->has('min_age') && $request->has('max_age'), function ($query) use ($request) {
                    $minAge = $request->input('min_age');
                    $maxAge = $request->input('max_age');
    
                    // Adjust the condition to include the specified age range
                    $query->where(function ($subQuery) use ($minAge, $maxAge) {
                        $subQuery->whereBetween('age', [$minAge, $maxAge])
                            ->orWhere('age', $minAge)
                            ->orWhere('age', $maxAge);
                    });
                })
                ->when($request->has('nationality'), function ($query) use ($request) {
                    $query->whereHas('location', function ($subQuery) use ($request) {
                        $subQuery->where('nationality', $request->input('nationality'));
                    });
                })
                ->when($request->has('country'), function ($query) use ($request) {
                    $query->whereHas('location', function ($subQuery) use ($request) {
                        $subQuery->where('country', $request->input('country'));
                    });
                })
                ->when($request->has('length'), function ($query) use ($request) {
                    $query->whereHas('personalInformation', function ($subQuery) use ($request) {
                        $subQuery->where('length', $request->input('length'));
                    });
                })
                ->when($request->has('weight'), function ($query) use ($request) {
                    $query->whereHas('personalInformation', function ($subQuery) use ($request) {
                        $subQuery->where('weight', $request->input('weight'));
                    });
                })
                ->when($request->has('skin_colour'), function ($query) use ($request) {
                    $query->whereHas('personalInformation', function ($subQuery) use ($request) {
                        $subQuery->where('skin_colour', $request->input('skin_colour'));
                    });
                })
                ->get();
        }
        

        $users_data = UserResource::collection($users);
        return $this->returnData('data',$users_data);
    }
}
