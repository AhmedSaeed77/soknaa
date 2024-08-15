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
        if($this->resource['message']['type'] == 0)
        {
            return [
                    'id' => $this->resource['message']['id'],
                    'type' => $this->resource['type'],
                    'is_seen' => $this->resource['message']['is_seen'],
                    'image' => 0,
                    'date' => $this->resource['message']['created_at']->format('l, H:i'),
                    'message' => $this->resource['message']['message'],
                ];
        }
        else
        {
            return [
                    'id' => $this->resource['message']['id'],
                    'type' => $this->resource['type'],
                    'is_seen' => $this->resource['message']['is_seen'],
                    'image' => 1,
                    'date' => $this->resource['message']['created_at']->format('l, H:i'),
                    'message' => url($this->resource['message']['message']),
                ];
        }
        
    }
}
