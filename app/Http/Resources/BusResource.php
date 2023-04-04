<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Bus */
class BusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->hash_id,
            'name'  => $this->name,
            'seats' => SeatResource::collection($this->whenLoaded('seats')),
        ];
    }
}
