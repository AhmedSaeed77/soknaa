<?php

namespace App\Http\Resources\api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatRoomResource extends JsonResource
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
            'other_party' => $this->otherParty?->user,
            'content' => $this->latestMessageContent,
            'sent_at' => Carbon::parse($this->last_seen?->created_at)->format('d M Y h:ia'),
            'unread_count' => $this->unreadCount,
        ];
    }
}
