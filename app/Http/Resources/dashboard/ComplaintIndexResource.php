<?php

namespace App\Http\Resources\dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplaintIndexResource extends JsonResource
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
                    'complaint_num' => $this->complaint_num,
                    'from' => $this->from,
                    'from_name' => $this->fromUser->name,
                    'membership_num' => $this->fromUser->membership_num,
                    'user_image' => $this->fromUser?->images->first() ? url($this->fromUser?->images->first()->image) : null,
                    'to' => $this->to,
                    'to_name' => $this->toUser->name,
                    'membership_num' => $this->toUser->membership_num,
                    'date' => $this->created_at->format('Y-m-d'),
                    'complaint' => $this->complaint,
                ];
    }
}
