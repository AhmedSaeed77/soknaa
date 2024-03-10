<?php

namespace App\Http\Resources\dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardOrderResource extends JsonResource
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
                    'name' => $this->fromUser->name,
                    'country' => $this->fromUser->location->country,
                    'membership_num' => $this->fromUser->membership_num,
                    'email' => $this->fromUser->email,
                    'type' => $this->fromUser->type,
                    'sex' => $this->fromUser->sex,
                    'phone' => $this->fromUser->phone,
                    'date' => $this->fromUser->created_at->format('Y-m-d'),
                    'status' => $this->status,
                ];
    }
}
