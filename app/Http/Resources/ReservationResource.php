<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Reservation */
class ReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->hash_id,
            'bus'         => new BusResource($this->whenLoaded('bus')),
            'fromStation' => new TripStationResource($this->whenLoaded('fromStation')),
            'toStation'   => new TripStationResource($this->whenLoaded('toStation')),
            'trip'        => new TripResource($this->whenLoaded('trip')),
        ];
    }
}
