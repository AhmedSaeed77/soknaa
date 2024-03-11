<?php

namespace App\Http\Resources\dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardOneOrderResource extends JsonResource
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
                    'order_num' => $this->order_num,
                    'from' => $this->from,
                    'from_name' => $this->fromUser->name,
                    'from_membership_num' => $this->fromUser->membership_num,
                    'from_email' => $this->fromUser->email,
                    'from_type' => $this->fromUser->type,
                    'from_sex' => $this->fromUser->sex,
                    'from_phone' => $this->fromUser->phone,
                    'from_familysitiation' => $this->fromUser->familysitiation,
                    'from_parentphone' => $this->fromUser->parent_id ?? null,
                    'from_typemerrage' => $this->fromUser->typemerrage ?? null,
                    'from_child_num' => $this->fromUser->child_num ?? null,
                    'from_age' => $this->fromUser->age ?? null,
                    'from_country' => $this->fromUser->location->country ?? null,
                    'from_city' => $this->fromUser->location->city ?? null,
                    'from_nationality' => $this->fromUser->location->nationality ?? null,
                    'from_religion' => $this->fromUser->location->religion ?? null,

                    'weight' => $this->fromUser->personalInformation->weight ?? null,
                    'length' => $this->fromUser->personalInformation->length ?? null,
                    'skin_colour' => $this->fromUser->personalInformation->skin_colour ?? null,
                    'physique' => $this->fromUser->personalInformation->physique ?? null,
                    'health_statuse' => $this->fromUser->personalInformation->health_statuse ?? null,
                    'religion' => $this->fromUser->personalInformation->religion ?? null,
                    'prayer' => $this->fromUser->personalInformation->prayer ?? null,
                    'smoking' => $this->fromUser->personalInformation->smoking ?? null,
                    'beard' => $this->fromUser->personalInformation->beard ?? null,
                    'hijab' => $this->fromUser->personalInformation->hijab ?? null,
                    'educational_level' => $this->fromUser->personalInformation->educational_level ?? null,
                    'financial_statuse' => $this->fromUser->personalInformation->financial_statuse ?? null,
                    'employment' => $this->fromUser->personalInformation->employment ?? null,
                    'job' => $this->fromUser->personalInformation->job ?? null,
                    'monthly_income' => $this->fromUser->personalInformation->monthly_income ?? null,
                    'life_partner_info' => $this->fromUser->personalInformation->life_partner_info ?? null,
                    'my_information' => $this->fromUser->personalInformation->my_information ?? null,
                    // 'from_image' => url($this->fromUser->images->first()->image),
                    'from_images' => ImageUserResource::collection($this->fromUser->images),

                    'to' => $this->to,
                    'to_name' => $this->toUser->name,
                    'to_membership_num' => $this->toUser->membership_num,
                    'to_email' => $this->toUser->email,
                    'to_type' => $this->toUser->type,
                    'to_sex' => $this->toUser->sex,
                    'to_phone' => $this->toUser->phone,
                    'to_familysitiation' => $this->toUser->familysitiation,

                    'to_parentphone' => $this->toUser->parent_id ?? null,
                    'to_typemerrage' => $this->toUser->typemerrage ?? null,
                    'to_child_num' => $this->toUser->child_num ?? null,
                    'to_age' => $this->toUser->age ?? null,
                    'to_country' => $this->toUser->location->country ?? null,
                    'to_city' => $this->toUser->location->city ?? null,
                    'to_nationality' => $this->toUser->location->nationality ?? null,
                    'to_religion' => $this->toUser->location->religion ?? null,

                    'weight' => $this->toUser->personalInformation->weight ?? null,
                    'length' => $this->toUser->personalInformation->length ?? null,
                    'skin_colour' => $this->toUser->personalInformation->skin_colour ?? null,
                    'physique' => $this->toUser->personalInformation->physique ?? null,
                    'health_statuse' => $this->toUser->personalInformation->health_statuse ?? null,
                    'religion' => $this->toUser->personalInformation->religion ?? null,
                    'prayer' => $this->toUser->personalInformation->prayer ?? null,
                    'smoking' => $this->toUser->personalInformation->smoking ?? null,
                    'beard' => $this->toUser->personalInformation->beard ?? null,
                    'hijab' => $this->toUser->personalInformation->hijab ?? null,
                    'educational_level' => $this->toUser->personalInformation->educational_level ?? null,
                    'financial_statuse' => $this->toUser->personalInformation->financial_statuse ?? null,
                    'employment' => $this->toUser->personalInformation->employment ?? null,
                    'job' => $this->toUser->personalInformation->job ?? null,
                    'monthly_income' => $this->toUser->personalInformation->monthly_income ?? null,
                    'life_partner_info' => $this->toUser->personalInformation->life_partner_info ?? null,
                    'my_information' => $this->toUser->personalInformation->my_information ?? null,

                    // 'to_image' => url($this->toUser->images->first()->image),
                    'to_images' => ImageUserResource::collection($this->toUser->images),

                    'status' => $this->status,
                    
                ];
    }
}
