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
                    'type' => $this->type,
                    'sex' => $this->sex,
                    'membership_num' => $this->membership_num,
                    'image' => url($this->images->first()->image),
                ];
    }
}
