<?php

namespace App\Http\Resources\api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OneUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isHaveChat = \App\Models\Order::where(function ($query) {
            $query->where('from', $this->id)
                  ->orWhere('to', $this->id);
        })
        ->where(function ($query) {
            $query->where('from', auth()->user()->id)
                  ->orWhere('to', auth()->user()->id);
        })
        ->exists() ? 1 : 0;
        $order = \App\Models\Order::where(function ($query) {
            $query->where('from', $this->id)
                  ->orWhere('to', $this->id);
        })
        ->where(function ($query) {
            $query->where('from', auth()->user()->id)
                  ->orWhere('to', auth()->user()->id);
        })
        ->latest()->first();


        $lastSeen = $this->last_seen; // assuming it's a timestamp or datetime object
        $lastRegistered = $this->created_at; // assuming it's a timestamp or datetime object

        // Set locale to Arabic
        \Carbon\Carbon::setLocale('ar');

        // Calculate the difference and format it
        $lastSeenCarbon = Carbon::parse($lastSeen);
        $lastRegisteredCarbon = Carbon::parse($lastRegistered);

        if ($lastSeenCarbon->diffInDays() > 0) {
            // If the difference is more than or equal to 1 day
            $lastSeenFormatted = $lastSeenCarbon->diffForHumans(null, true, false, 2); // "منذ يوم" or "منذ 7 أيام"
        } else {
            // If the difference is less than a day, return "منذ 3 ساعات" etc.
            $lastSeenFormatted = $lastSeenCarbon->diffForHumans(null, true, false, 3);
        }

        if ($lastRegisteredCarbon->diffInDays() > 0) {
            $lastRegisteredFormatted = $lastRegisteredCarbon->diffForHumans(null, true, false, 2);
        } else {
            $lastRegisteredFormatted = $lastRegisteredCarbon->diffForHumans(null, true, false, 3);
        }


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
                    'is_online' => $this->is_online,

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
                    'is_have_chat' => $isHaveChat,
                    'last_seen' => $lastSeenFormatted ?? null,
                    'last_registered' => $lastRegisteredFormatted ?? null,
                    'order_id' => $order ? $order->id : null,
                    'flag' => $this->location->country ? url(DB::table('all_countries')->where('country_arName', $this->location->country)->select('image')->first()->image) : null,
                    'images' => ImageUserResource::collection($this->images),
                    // 'image' => $this->images->first() ? url($this->images->first()->image) : null,
                ];
    }
}
