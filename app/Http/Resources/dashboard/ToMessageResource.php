<?php

namespace App\Http\Resources\dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ToMessageResource extends JsonResource
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
                    'date' => $this->created_at->format('l, H:i'),
                    'message' => $this->message,
                ];
    }
}
