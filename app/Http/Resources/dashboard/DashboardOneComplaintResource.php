<?php

namespace App\Http\Resources\dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardOneComplaintResource extends JsonResource
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
                    'date' => $this->created_at->format('Y-m-d'),
                    'complaint' => $this->complaint,

                    'from' => $this->from,
                    'from_name' => $this->fromUser->name,
                    'from_membership_num' => $this->fromUser->membership_num,
                    'from_familysitiation' => $this->fromUser->familysitiation,
                    'from_phone' => $this->fromUser->phone,
                    'from_gender' => $this->fromUser->sex,

                    'to' => $this->to,
                    'to_name' => $this->toUser->name,
                    'to_membership_num' => $this->toUser->membership_num,
                    'to_familysitiation' => $this->toUser->familysitiation,
                    'to_phone' => $this->toUser->phone,
                    'to_gender' => $this->toUser->sex,


                ];
    }
}
