<?php

namespace App\Http\Resources\dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardComplaintResource extends JsonResource
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
                    'to' => $this->to,
                    'to_name' => $this->toUser->name,
                    'membership_num' => $this->toUser->membership_num,
                    'date' => $this->created_at->format('Y-m-d'),
                    'complaint' => $this->complaint,
                ];
    }
}
