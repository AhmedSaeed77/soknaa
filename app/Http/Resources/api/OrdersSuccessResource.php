<?php

namespace App\Http\Resources\api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdersSuccessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        if($this->message_from == 2)
        {
            return [
                    'id' => $this->id,
                    'order_num' => $this->order_num,
                    'from' => $this->to,
                    'from_name' => $this->toUser->name,
                    'from_membership_num' => $this->toUser->membership_num,
                    'from_email' => $this->toUser->email,
                    'from_country' => $this->toUser->location->country,
                    'from_type' => $this->toUser->type,
                    'from_sex' => $this->toUser->sex,
                    'from_phone' => $this->toUser->phone,
                    'from_familysitiation' => $this->toUser->familysitiation,
                    // 'from_image' => url($this->fromUser->images->first()->image),
                    'from_image' => $this->toUser->images->first() ? url($this->toUser->images->first()->image) : null,
                    'date' => $this->updated_at->format('Y-m-d'),
                    'message' => $this->message ? $this->message : '',
                ];
        }
        elseif($this->message_from == 1)
        {
             return [
                        'id' => $this->id,
                        'order_num' => $this->order_num,
                        'from' => $this->from,
                        'from_name' => $this->fromUser->name,
                        'from_membership_num' => $this->fromUser->membership_num,
                        'from_email' => $this->fromUser->email,
                        'from_country' => $this->fromUser->location->country,
                        'from_type' => $this->fromUser->type,
                        'from_sex' => $this->fromUser->sex,
                        'from_phone' => $this->fromUser->phone,
                        'from_familysitiation' => $this->fromUser->familysitiation,
                        // 'from_image' => url($this->fromUser->images->first()->image),
                        'from_image' => $this->fromUser->images->first() ? url($this->fromUser->images->first()->image) : null,
                        'date' => $this->updated_at->format('Y-m-d'),
                        'message' => $this->message ? $this->message : '',
                    ];
        }
        else
        {
            return [
                'id' => $this->id,
                'order_num' => null,
                'from' => $this->id,
                'from_name' => $this->name,
                'from_membership_num' => $this->membership_num,
                'from_email' => $this->email,
                'from_country' => $this->location->country,
                'from_type' => $this->type,
                'from_sex' => $this->sex,
                'from_phone' => $this->phone,
                'from_familysitiation' => $this->familysitiation,
                // 'from_image' => url($this->fromUser->images->first()->image),
                'from_image' => $this->images->first() ? url($this->images->first()->image) : null,
                'date' => $this->updated_at->format('Y-m-d'),
                'message' => $this->reason ? $this->reason : '',
            ];
        }

    }
}
