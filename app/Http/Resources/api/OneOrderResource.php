<?php

namespace App\Http\Resources\api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OneOrderResource extends JsonResource
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
                    'to' => $this->to,
                    'from' => $this->from,
                    'name' => $this->toUser->name,
                    'membership_num' => $this->toUser->membership_num,
                    'email' => $this->toUser->email,
                    'type' => $this->toUser->type,
                    'sex' => $this->toUser->sex,
                    'phone' => $this->toUser->phone,
                    'familysitiation' => $this->toUser->familysitiation,
                    'country' => $this->toUser->location->country,
                    'date' => $this->toUser->created_at->format('Y-m-d'),
                    'image' => url($this->toUser->images->first()->image),
                    'status' => $this->status,
                ];
    }
}
