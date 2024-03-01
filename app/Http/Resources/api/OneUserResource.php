<?php

namespace App\Http\Resources\api;

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
                    'is_ordered' => $this->is_ordered,
                    'name' => $this->name,
                    'nickname ' => $this->nickname,
                    'phone ' => $this->phone,
                    'parent_phone ' => $this->parent_phone,
                    'type' => $this->type,
                    'age' => $this->age,
                    'child_num' => $this->child_num,
                    'membership_num' => $this->membership_num,
                    'status' => $this->status,
                    'sex' => $this->sex,
                    'typemerrage' => $this->typemerrage,
                    'familysitiation' => $this->familysitiation,

                    'country' => $this->location->country ?? null,
                    'nationality' => $this->location->nationality ?? null,
                    'city' => $this->location->city ?? null,
                    'religion' => $this->location->religion ?? null,

                    'weight' => $this->personalInformation->weight ?? null,
                    'length' => $this->personalInformation->length ?? null,
                    'skin_colour' => $this->personalInformation->skin_colour ?? null,
                    'physique' => $this->personalInformation->physique ?? null,
                    'health_statuse' => $this->personalInformation->health_statuse ?? null,
                    'religion' => $this->personalInformation->religion ?? null,
                    'prayer' => $this->personalInformation->prayer ?? null,
                    'smoking' => $this->personalInformation->smoking ?? null,
                    'beard' => $this->personalInformation->beard ?? null,

                    'hijab' => $this->personalInformation->hijab ?? null,
                    'educational_level' => $this->personalInformation->educational_level ?? null,
                    'financial_statuse' => $this->personalInformation->financial_statuse ?? null,
                    'employment' => $this->personalInformation->employment ?? null,
                    'job' => $this->personalInformation->job ?? null,
                    'monthly_income' => $this->personalInformation->monthly_income ?? null,
                    'life_partner_info' => $this->personalInformation->life_partner_info ?? null,
                    'my_information' => $this->personalInformation->my_information ?? null,
                    
                    // 'images' => ImageUserResource::collection($this->images),
                    'image' => $this->images->first() ? url($this->images->first()->image) : null,
                ];
    }
}
