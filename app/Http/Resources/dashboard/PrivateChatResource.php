<?php

namespace App\Http\Resources\dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrivateChatResource extends JsonResource
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
                    'phone' => $this->phone,
                    'country' => $this->location?->country,
                    'message' => $this->latestPrivateChat()?->message,
                    'date' => $this->latestPrivateChat()?->created_at->format('h:i A'),
                ];
    }
}
