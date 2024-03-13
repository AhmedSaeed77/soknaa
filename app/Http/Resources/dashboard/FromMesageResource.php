<?php

namespace App\Http\Resources\dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FromMesageResource extends JsonResource
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
                    'id' => $this->resource['message']['id'],
                    'type' => $this->resource['type'],
                    'date' => $this->resource['message']['created_at']->format('l, H:i'),
                    'message' => $this->resource['message']['message'],
                ];
    }
}
