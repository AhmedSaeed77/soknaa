<?php

namespace App\Http\Resources\api;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        $countryName = $this->location->country;

        if ($countryName)
        {
            $country = DB::table('all_countries')
                ->where('country_arName', $countryName)
                ->select('image')
                ->first();
        }
        
        return [
                    'id' => $this->id,
                    'is_ordered' => $this->is_ordered,
                    'name' => $this->name,
                    'nickname' => $this->nickname,
                    'type' => $this->type,
                    'gender' => $this->sex,
                    'familysitiation' => $this->familysitiation ?? null,
                    'country' => $this->location->country ?? null,
                    'membership_num' => $this->membership_num,
                    'is_showprofile' => $this->is_showprofile,
                    'is_online' => $this->is_online,
                    // 'image' => url($this->images->first()->image),
                    'image' => $this->images->first() ? url($this->images->first()->image) : null,
                    'flag' => $country ? url($country->image) : null,
                ];
    }
}
