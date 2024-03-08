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
                    'from_parentphone' => $this->fromUser->parent_id ?? null,
                    'from_typemerrage' => $this->fromUser->typemerrage ?? null,
                    'from_child_num' => $this->fromUser->child_num ?? null,
                    'from_age' => $this->fromUser->age ?? null,
                    'from_country' => $this->fromUser->location->country ?? null,
                    'from_city' => $this->fromUser->location->city ?? null,
                    'from_nationality' => $this->fromUser->location->nationality ?? null,
                    'from_religion' => $this->fromUser->location->religion ?? null,
                    // 'from_image' => url($this->fromUser->images->first()->image),
                    'from_images' => ImageUserResource::collection($this->fromUser->images),

                    'to' => $this->to,
                    'to_name' => $this->toUser->name,
                    'to_membership_num' => $this->toUser->membership_num,
                    'to_email' => $this->toUser->email,
                    'to_type' => $this->toUser->type,
                    'to_sex' => $this->toUser->sex,
                    'to_phone' => $this->toUser->phone,
                    'to_familysitiation' => $this->toUser->familysitiation,

                    'to_parentphone' => $this->toUser->parent_id ?? null,
                    'to_typemerrage' => $this->toUser->typemerrage ?? null,
                    'to_child_num' => $this->toUser->child_num ?? null,
                    'to_age' => $this->toUser->age ?? null,
                    'to_country' => $this->toUser->location->country ?? null,
                    'to_city' => $this->toUser->location->city ?? null,
                    'to_nationality' => $this->toUser->location->nationality ?? null,
                    'to_religion' => $this->toUser->location->religion ?? null,
                    // 'to_image' => url($this->toUser->images->first()->image),
                    'to_images' => ImageUserResource::collection($this->toUser->images),

                    'status' => $this->status,
                    
                ];
    }
}
