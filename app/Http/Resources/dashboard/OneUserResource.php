<?php

namespace App\Http\Resources\dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OneUserResource extends JsonResource
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
                    'name' => $this->name,
                    'nickname' => $this->nickname,
                    'phone' => $this->phone,
                    'parent_phone' => $this->parent_phone,
                    'type' => $this->type,
                    'age' => $this->age,
                    'child_num' => $this->child_num,
                    'membership_num' => $this->membership_num,
                    'status' => $this->status,
                    'gender' => $this->sex,
                    'typemerrage' => $this->typemerrage,
                    'familysitiation' => $this->familysitiation,

                    'country' => $this->location->country,
                    'nationality' => $this->location->nationality,
                    'city' => $this->location->city,
                    'religion' => $this->location->religion,

                    'weight' => $this->personalInformation->weight,
                    'length' => $this->personalInformation->length,
                    'skin_colour' => $this->personalInformation->skin_colour,
                    'physique' => $this->personalInformation->physique,
                    'health_statuse' => $this->personalInformation->health_statuse,
                    'religion' => $this->personalInformation->religion,
                    'prayer' => $this->personalInformation->prayer,
                    'smoking' => $this->personalInformation->smoking,
                    'beard' => $this->personalInformation->beard,

                    'hijab' => $this->personalInformation->hijab,
                    'educational_level' => $this->personalInformation->educational_level,
                    'financial_statuse' => $this->personalInformation->financial_statuse,
                    'employment' => $this->personalInformation->employment,
                    'job' => $this->personalInformation->job,
                    'monthly_income' => $this->personalInformation->monthly_income,
                    'life_partner_info' => $this->personalInformation->life_partner_info,
                    'my_information' => $this->personalInformation->my_information,
                    
                    'images' => ImageUserResource::collection($this->images),
                ];
    }
}
