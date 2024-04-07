<?php

namespace App\Http\Controllers\dashboard;
use App\Models\Order;
use App\Models\User;
use App\Models\Chat;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\dashboard\ChangeStatusOrderRequest;
use App\Http\Resources\dashboard\DashboardOrderResource;
use App\Http\Resources\dashboard\DashboardOneOrderResource;
use App\Http\Resources\dashboard\OrdersIndexResource;
use App\Http\Resources\dashboard\ChatIndexResource;

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

            $malecounter = User::where('type','زوج')->when($request->date == 1, function ($query) {
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

            $fmalecounter = User::where('type','زوجه')->when($request->date == 1, function ($query) {
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

            $chats = Chat::whereNotNull('from_user')->orderBy('created_at', 'desc')->take(5)->get();
            foreach($chats as $chat)
            {
                $chat->flag = $this->getTypeOrder($chat->fromUser->id);
                if($chat->flag == 1)
                {
                    $chat->order_id = $this->getOrderIdFrom($chat->fromUser->id);
                }
                else
                {
                    $chat->order_id = $this->getOrderIdTo($chat->toUser->id);
                }
                
            }
            $chats_data = ChatIndexResource::collection($chats);
            $data = [
                        'allusers' => $allusers,
                        'malecounter' => $malecounter,
                        'fmalecounter' => $fmalecounter,
                        'financecounter' => $financecounter,
                        'ordercounter' => $ordercounter,
                        'ordersuccesscounter' => $ordersuccesscounter,
                        'orders_data' => $orders_data,
                        'chats_data' => $chats_data,
                    ];
            return $this->returnData('data',$data);
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function getTypeOrder($id)
    {
        $userId = $id;
        $lastOrder = Order::latest()->first();
        $receiverType = null;
        if ($lastOrder)
        {
            if ($lastOrder->from == $userId)
            {
                $receiverType = 1;
            }
            elseif ($lastOrder->to == $userId)
            {
                $receiverType = 2;
            }
        }
        if($lastOrder)
        {
            if (!$receiverType)
            {
                $previousOrders = Order::where('id', '<', $lastOrder->id)->latest()->get();
                foreach ($previousOrders as $order) 
                {
                    if ($order->from == $userId)
                    {
                        $receiverType = 1;
                        break;
                    }
                    elseif ($order->to == $userId)
                    {
                        $receiverType = 2;
                        break;
                    }
                }
            }
        }
        return $receiverType;
    }

    public function getOrderIdFrom($id)
    {
        $userId = $id;
        $last_order = Order::where('from', $userId)
                   ->orderBy('order_date', 'desc')
                   ->first();
    }

    public function getOrderIdTo($id)
    {
        $userId = $id;
        $last_order = Order::where('to', $userId)
                   ->orderBy('order_date', 'desc')
                   ->first();
    }

}
