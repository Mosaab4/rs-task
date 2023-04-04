<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\TripStation */
class TripStationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this->hash_id,
            'step' => $this->step,
            'city' => new CityResource($this->whenLoaded('city')),
        ];
    }
}
