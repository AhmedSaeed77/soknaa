<?php

namespace App\Http\Resources\dashboard;

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
                    'membership_num' => $this->membership_num,
                    'email' => $this->email,
                    'type' => $this->type,
                    'gender' => $this->sex,
                    'phone' => $this->phone,
                    'date' => $this->created_at->format('Y-m-d'),
                    'status' => $this->status,
                    'block' => $this->block == 0 ? 'غير مفعل' : 'مفعل',
                ];
    }
}
