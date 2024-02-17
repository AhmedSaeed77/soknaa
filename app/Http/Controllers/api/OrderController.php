<?php

namespace App\Http\Controllers\api;
use App\Models\Order;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\OrderRequest;
use Illuminate\Http\Request;
use App\Http\Resources\api\OrdersResource;
use App\Http\Resources\api\OneOrderResource;

use App\Traits\GeneralTrait;

class OrderController extends Controller
{
    use GeneralTrait;

    public function createOrder(OrderRequest $request)
    {
        try
        {
            $old_order = Order::where('from',auth()->user()->id)->where('status',0)->first();
            if($old_order)
            {
                return $this->returnError('',__('site.can_not_do_another_request'));
            }
            Order::create([
                                'from' => auth()->user()->id,
                                'to' => $request->to_user,
                        ]);
            return $this->returnData('data',__('dashboard.recored created successfully.'),__('dashboard.recored created successfully.'));
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getAllorders()
    {
        try
        {
            $orders = Order::where('from',auth()->user()->id)->orderBy('created_at', 'desc')->get();
            $orders_data = OrdersResource::collection($orders);
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
            if($order)
            {
                $order_data = new OneOrderResource($order);
                return $this->returnData('data',$order_data);
            }
            return $this->returnError('',__('site.Order_Not_Found'));
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
