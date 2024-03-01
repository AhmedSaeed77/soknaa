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

    public function getAllorders()
    {
        try
        {
            $orders = Order::all();
            $orders_data = DashboardOrderResource::collection($orders);
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
                $order->update([ 'status' => $request->status]);
                $user = User::find($order->from);
                $user->update(['is_ordered' => 0]);
                return $this->returnData('data',__('dashboard.item_is_updated'),__('dashboard.item_is_updated'));
            }
            return $this->returnError('',__('site.Order_Not_Found'));
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
