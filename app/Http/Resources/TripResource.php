<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Trip */
class TripResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->hash_id,
            'name'      => $this->name,
            'from_city' => new CityResource($this->whenLoaded('fromCity')),
            'to_city'   => new CityResource($this->whenLoaded('toCity')),
            'bus'       => new BusResource($this->whenLoaded('bus')),
            'stations'  => TripStationResource::collection($this->whenLoaded('stations')),
        ];
    }
}
