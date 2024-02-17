<?php

namespace App\Http\Resources\dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardOneOrderResource extends JsonResource
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
                    'from_membership_num' => $this->fromUser->membership_num,
                    'from_email' => $this->fromUser->email,
                    'from_type' => $this->fromUser->type,
                    'from_sex' => $this->fromUser->sex,
                    'from_phone' => $this->fromUser->phone,
                    'from_familysitiation' => $this->fromUser->familysitiation,
                    'from_image' => url($this->fromUser->images->first()->image),

                    'to' => $this->to,
                    'to_name' => $this->toUser->name,
                    'to_membership_num' => $this->toUser->membership_num,
                    'to_email' => $this->toUser->email,
                    'to_type' => $this->toUser->type,
                    'to_sex' => $this->toUser->sex,
                    'to_phone' => $this->toUser->phone,
                    'to_familysitiation' => $this->toUser->familysitiation,
                    'to_image' => url($this->toUser->images->first()->image),

                    'status' => $this->status,
                    
                ];
    }
}
