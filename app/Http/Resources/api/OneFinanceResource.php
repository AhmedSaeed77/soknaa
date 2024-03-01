<?php

namespace App\Http\Resources\api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OneFinanceResource extends JsonResource
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
                    'is_ordered' => $this->is_ordered,
                    'name' => $this->name,
                    'nickname ' => $this->nickname,
                    'phone ' => $this->phone,
                    'parent_phone ' => $this->parent_phone,
                    'type' => $this->type,
                    'age' => $this->age,
                    'child_num' => $this->child_num,
                    'membership_num' => $this->membership_num,
                    'status' => $this->status,
                    'sex' => $this->sex,
                    'typemerrage' => $this->typemerrage,
                    'familysitiation' => $this->familysitiation,
                    
                    // 'images' => ImageUserResource::collection($this->images),
                    'image' => $this->images->first() ? url($this->images->first()->image) : null,
                ];
    }
}
