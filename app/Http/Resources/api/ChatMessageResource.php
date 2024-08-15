<?php

namespace App\Http\Resources\api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sender' => $this->user,
            'content' => $this->contentValue,
            'type' => $this->type,
            'sent_at' => Carbon::parse($this->created_at)->format('d M Y h:ia')
        ];
    }
}
