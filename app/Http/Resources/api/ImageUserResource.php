<?php

namespace App\Http\Resources\api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageUserResource extends JsonResource
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
                    'indexx' => $this->indexx,
                    'image' => url($this->image),
                ];
    }
}
