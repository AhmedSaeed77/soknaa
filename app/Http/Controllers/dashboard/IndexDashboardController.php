<?php

namespace App\Http\Controllers\dashboard;
use App\Models\Order;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\dashboard\ChangeStatusOrderRequest;
use App\Http\Resources\dashboard\DashboardOrderResource;
use App\Http\Resources\dashboard\DashboardOneOrderResource;
use App\Http\Resources\dashboard\OrdersIndexResource;

use App\Traits\GeneralTrait;

class IndexDashboardController extends Controller
{
    use GeneralTrait;

    public function index()
    {
        try
        {
            $allusers = User::count();
            $malecounter = User::where('type','زوج')->count();
            $fmalecounter = User::where('type','زوجه')->count();
            $financecounter = User::where('type','خاطبه')->count();

            $ordercounter = Order::count();
            $ordersuccesscounter = Order::where('status',1)->count();

            $orders = Order::orderBy('created_at', 'desc')->take(10)->get();
            $orders_data = OrdersIndexResource::collection($orders);

            $data = [
                        'allusers' => $allusers,
                        'malecounter' => $malecounter,
                        'fmalecounter' => $fmalecounter,
                        'financecounter' => $financecounter,
                        'ordercounter' => $ordercounter,
                        'ordersuccesscounter' => $ordersuccesscounter,
                        'orders_data' => $orders_data,
                    ];
            // $orders_data = DashboardOrderResource::collection($orders);
            return $this->returnData('data',$data);
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
