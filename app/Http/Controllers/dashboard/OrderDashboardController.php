<?php

namespace App\Http\Controllers\dashboard;
use App\Models\Order;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\dashboard\ChangeStatusOrderRequest;
use App\Http\Resources\dashboard\DashboardOrderResource;
use App\Http\Resources\dashboard\DashboardOneOrderResource;

use App\Traits\GeneralTrait;

class OrderDashboardController extends Controller
{
    use GeneralTrait;

    public function getAllorders(Request $request)
    {
        try
        {
            $search = $request->search;
            $country = $request->country;
            $orders = Order::when($search, function ($query) use ($search) {
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
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            $orders_data = DashboardOrderResource::collection($orders)->response()->getData(true);
            return $this->returnData('data',$orders_data);
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getOneOrder($id)
    {
        try
        {
            $order = Order::find($id);
            $order_data = new DashboardOneOrderResource($order);
            return $this->returnData('data',$order_data);
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function changestatus(ChangeStatusOrderRequest $request,$id)
    {
        try
        {
            $order = Order::find($id);
            if($order)
            {
                $order->update([ 'status' => $request->status,'message' => $request->message,'message_from' => $request->message_from]);
                $user1 = User::find($order->from);
                $user1->update(['is_ordered' => 0]);
                $user2 = User::find($order->to);
                $user2->update(['is_ordered' => 0]);
                if($request->status == 1)
                {
                    $user1->update(['is_showprofile' => 0]);
                    $user2->update(['is_showprofile' => 0]);
                }
                if($request->status == 2)
                {
                    $user1->update(['is_showprofile' => 1]);
                    $user2->update(['is_showprofile' => 1]);
                }
                return $this->returnData('data',__('dashboard.married_accept'),__('dashboard.married_accept'));
            }
            return $this->returnError('',__('site.Order_Not_Found'));
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
