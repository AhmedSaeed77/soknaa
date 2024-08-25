<?php

namespace App\Http\Controllers\dashboard;
use App\Models\Complaint;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\dashboard\DashboardOneComplaintResource;
use App\Http\Resources\dashboard\DashboardComplaintResource;
use App\Traits\GeneralTrait;

class ComplaintDashboardController extends Controller
{
    use GeneralTrait;

    public function getAllComplaints(Request $request)
    {
        try
        {
            $search = $request->search;
            $country = $request->country;
            $complaints = Complaint::when($search, function ($query) use ($search) {
                $query->whereHas('fromUser', function ($subquery) use ($search) {
                    $subquery->where('name', 'like', '%' . $search . '%');
                });
            })
            ->when($search, function ($query) use ($search) {
                $query->whereHas('fromUser', function ($subquery) use ($search) {
                    $subquery->where('nickname', 'like', '%' . $search . '%');
                });
            })
            ->when($country, function ($query) use ($country) {
                $query->whereHas('fromUser', function ($subquery) use ($country) {
                    $subquery->whereHas('location', function($subquery2) use ($country)
                    {
                        $subquery2->where('country', 'like', '%' . $country . '%');
                    });
                });
            })
            // ->when($country, function ($query) use ($country) {
            //     $query->whereHas('toUser', function ($subquery) use ($country) {
            //         $subquery->whereHas('location', function($subquery2) use ($country)
            //         {
            //             $subquery2->where('country', 'like', '%' . $country . '%');
            //         });
            //     });
            // })
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
            $complaints_data = DashboardComplaintResource::collection($complaints)->response()->getData(true);
            return $this->returnData('data',$complaints_data);
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getOneComplaint($id)
    {
        try
        {
            $complaint = Complaint::find($id);
            if($complaint)
            {
                $complaint_data = new DashboardOneComplaintResource($complaint);
                return $this->returnData('data',$complaint_data);
            }
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function blockUser($id)
    {
        try
        {
            $user = User::find($id);
            if($user)
            {
                $user->tokens()->delete();
                $user->is_removed = 1;
                $user->save();
                return $this->returnData('data',__('site.profile_blocked'), __('site.profile_blocked'));
            }
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
