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

    public function index(Request $request)
    {
        try
        {
            $allusers = User::
            when($request->date == 1, function ($query) {
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
            ->count();

            $malecounter = User::when($request->date == 1, function ($query) {
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
            ->where('type','زوج')->count();

            $fmalecounter = User::when($request->date == 1, function ($query) {
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
            ->where('type','زوجه')->count();
            $financecounter = User::where('type','خاطبه')->count();

            $ordercounter = Order::
            when($request->date == 1, function ($query) {
                return $query->whereDate('created_at', now()->toDateString());
            })
            ->when($request->date == 2, function ($query) {
                $startOfWeek = now()->startOfWeek();
                $endOfWeek = now()->endOfWeek();
                return $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            })
            ->when($request->date == 3, function ($query) {
                return $query->whereMonth('created_at', now()->month);
            })->count();
            $ordersuccesscounter = Order::where('status',1)->count();

            $orders = Order::when($request->date == 1, function ($query) {
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
            ->orderBy('created_at', 'desc')->take(10)->get();
            
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
