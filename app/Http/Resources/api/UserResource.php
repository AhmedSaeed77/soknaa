<?php

namespace App\Http\Resources\api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
                    'name' => $this->name,
                    'nickname' => $this->nickname,
                    'type' => $this->type,
                    'gender' => $this->sex,
                    'familysitiation' => $this->familysitiation ?? null,
                    'country' => $this->location->country ?? null,
                    'membership_num' => $this->membership_num,
                    // 'image' => url($this->images->first()->image),
                    'image' => $this->images->first() ? url($this->images->first()->image) : null,
                ];
    }
}
