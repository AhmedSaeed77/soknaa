<?php

namespace App\Http\Resources\dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatIndexResource extends JsonResource
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
                    'user_name' => $this->fromUser->name,
                    'user_image' => $this->fromUser->images->first() ? url($this->fromUser->images->first()->image) : null,
                    'message' => $this->message,
                    'flag' => $this->flag,
                    'order_id' => $this->order_id,
                    'date' => $this->created_at->format('h:i A'),
                ];
    }
}
