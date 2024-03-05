<?php

namespace App\Http\Resources\dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdersIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
                    'id' => $this->id,
                    'order_num' => $this->order_num,
                    'from' => $this->from,
                    'from_name' => $this->fromUser->name,

                    'to' => $this->to,
                    'to_name' => $this->toUser->name,

                    'phone' => $this->fromUser->phone,
                    'date' => $this->fromUser->created_at->format('Y-m-d'),
                    // 'status' => $this->status,
                ];
    }
}
