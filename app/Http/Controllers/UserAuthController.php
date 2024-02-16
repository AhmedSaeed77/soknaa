<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Image;
use App\Models\Location;
use App\Models\PersonalInformation;
use App\Http\Requests\api\UserLoginRequest;
use App\Http\Requests\api\RegisterRequest;
use App\Http\Requests\api\AddUserRequest;
use App\Http\Requests\api\UserResetRequest;
use App\Http\Requests\api\UserConfirmRequest;
use App\Http\Requests\api\UserChangePasswordDashboardRequest;
use App\Http\Requests\api\ShowProfileRequest;
use App\Http\Resources\api\UserResource;
use App\Http\Resources\api\OneUserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\GeneralTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Mail\NotifyMail;
use Mail;

class UserAuthController extends Controller
{
    use GeneralTrait;

    public function register(RegisterRequest $request)
    {
        try
        {
            if($request->type == 'خاطبه')
            {
                DB::beginTransaction();   
                User::create([
                                'name' => $request->name,
                                'email' => $request->email,
                                'password' => Hash::make($request->password),
                                'nickname' => $request->nickname,
                                'phone' => $request->phone,
                                'type' => $request->type,
                                'sex' => $request->sex,
                            ]);
                DB::commit();
                return $this->returnData('data',__('dashboard.recored created successfully.'),__('dashboard.recored created successfully.'));
            }
            else
            {
                DB::beginTransaction();
                $user = User::create([
                                        'name' => $request->name,
                                        'email' => $request->email,
                                        'password' => Hash::make($request->password),
                                        'nickname' => $request->nickname,
                                        'phone' => $request->phone,
                                        'type' => $request->type,
                                        'age' => $request->age,
                                        'child_num' => $request->child_num,
                                        'sex' => $request->sex,
                                        'typemerrage' => $request->typemerrage,
                                        'familysitiation' => $request->familysitiation,
                                    ]);

                Location::create([
                                    'user_id' => $user->id,
                                    'country' => $request->country,
                                    'nationality' => $request->nationality,
                                    'city' => $request->city,
                                    'religion' => $request->religion,
                                ]);

                PersonalInformation::create([
                                                'user_id' => $user->id,
                                                'weight' => $request->weight,
                                                'length' => $request->length,
                                                'skin_colour' => $request->skin_colour,
                                                'physique' => $request->physique,
                                                'health_statuse' => $request->health_statuse,
                                                'religion' => $request->religion,
                                                'prayer' => $request->prayer,
                                                'smoking' => $request->smoking,
                                                'beard' => $request->beard,
                                                'hijab' => $request->hijab,
                                                'educational_level' => $request->educational_level,
                                                'financial_statuse' => $request->financial_statuse,
                                                'employment' => $request->employment,
                                                'job' => $request->job,
                                                'monthly_income' => $request->monthly_income,
                                                'life_partner_info' => $request->life_partner_info,
                                                'my_information' => $request->my_information,
                                            ]);

                if($request->hasFile('images'))
                {
                    $i=0;
                    foreach($request->file('images') as $image)
                    {
                        $fileimage = $this->handle('images.'.$i, 'users');
                        Image::create([
                                        'user_id' => $user->id,
                                        'image' => $fileimage,
                                    ]);
                        $i++;
                    }
                }
            }
                                   
            DB::commit();
            return $this->returnData('data',__('dashboard.recored created successfully.'),__('dashboard.recored created successfully.'));
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function login(UserLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $token = Auth::guard('web')->attempt($credentials);
        if($token)
        {
            $user = User::where('email',$request->email)->first();
            if (\auth('web')->user()->is_active == 0)
            {
                return $this->returnError(422,__('dashboard.admin_not_active'));
            }
                
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
                DB::table('password_reset_tokens')->where('email',$request->email)->delete();
                DB::table('password_reset_tokens')->insert(['email' => $request->email , 'token' => $randomNumber]);
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
            $reset = DB::table('password_reset_tokens')->where('token',$request->confirm)->first();
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
            $user = User::where('email',$request->email)->first();
            if($user)
            {
                $user->update(['password' => Hash::make($request->password)]);
                DB::table('password_reset_tokens')->where('email',$request->email)->delete();
                return $this->returnData('data',__('site.password_Is_Changed'),__('site.password_Is_Changed'));
            }
            return $this->returnError('',__('site.User_Not_Found'));
        }
        catch (\Exception  $e)
        {
            return response()->json($e->getMessage(), 401);
        }
    }

    public function adduser(AddUserRequest $request)
    {
        DB::beginTransaction();  
        try
        {
            $user = User::create([
                                    'name' => $request->name,
                                    'parent_phone' => $request->parent_phone,
                                    'phone' => $request->phone,
                                    'type' => $request->type,
                                    'sex' => $request->sex,
                                    'age' => $request->age,
                                    'child_num' => $request->child_num,
                                    'sex' => $request->sex,
                                    'typemerrage' => $request->typemerrage,
                                    'familysitiation' => $request->familysitiation,
                                    'parent_id' => auth()->user()->id
                                ]);

            Location::create([
                                'user_id' => $user->id,
                                'country' => $request->country,
                                'nationality' => $request->nationality,
                                'city' => $request->city,
                                'religion' => $request->religion,
                            ]);

            PersonalInformation::create([
                                            'user_id' => $user->id,
                                            'weight' => $request->weight,
                                            'length' => $request->length,
                                            'skin_colour' => $request->skin_colour,
                                            'physique' => $request->physique,
                                            'health_statuse' => $request->health_statuse,
                                            'religion' => $request->religion,
                                            'prayer' => $request->prayer,
                                            'smoking' => $request->smoking,
                                            'beard' => $request->beard,
                                            'hijab' => $request->hijab,
                                            'educational_level' => $request->educational_level,
                                            'financial_statuse' => $request->financial_statuse,
                                            'employment' => $request->employment,
                                            'job' => $request->job,
                                            'monthly_income' => $request->monthly_income,
                                            'life_partner_info' => $request->life_partner_info,
                                            'my_information' => $request->my_information,
                                        ]);  
            if($request->hasFile('images'))
            {
                $i=0;
                foreach($request->file('images') as $image)
                {
                    $fileimage = $this->handle('images.'.$i, 'users');
                    Image::create([
                                    'user_id' => $user->id,
                                    'image' => $fileimage,
                                ]);
                    $i++;
                }
            }
            DB::commit();
            return $this->returnData('data',__('dashboard.recored created successfully.'),__('dashboard.recored created successfully.'));                                       
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getAllUserByFinance()
    {
        $users = User::where('parent_id','!=',null)->where('parent_id',auth()->user()->id)->get();
        $users_data = UserResource::collection($users);
        return $this->returnData('data',$users_data);
    }

    public function isShowProfile(ShowProfileRequest $request)
    {
        $user = User::find(auth()->user()->id);
        if($user->type == 'خاطبه')
        {
            $newuser = User::find($request->user_id);
            if($newuser)
            {
                $newuser->update(['is_showprofile' => $request->is_showprofile]);
                return $this->returnData('data',__('site.User_Profile_Updated'), __('site.User_Profile_Updated'));
            }
            return $this->returnError('',__('site.User_Not_Found'));
        }
        else
        {
            return $this->returnError('',__('site.type_of_user_not_suitable'));
        }
    }

    public function getOneUser($id)
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
}
