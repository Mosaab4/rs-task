<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Trip;
use App\Http\Controllers\Controller;
use App\Http\Resources\TripResource;

class TripController extends Controller
{
    public function index()
    {
        $trips = Trip::query()
            ->with(['fromCity:id,name', 'toCity:id,name'])
            ->orderBy('id', 'DESC')
            ->paginate();

        return $this->respondWithPagination(
            data: TripResource::collection($trips->items()),
            paginator: $trips
        );
    }

    public function show(Trip $trip)
    {
        $trip->load([
            'bus:id,name',
            'stations.city',
            'toCity:id,name',
            'fromCity:id,name',
            'bus.seats:bus_id,id,hash_id'
        ]);

        return $this->respondWithSuccess(new TripResource($trip));
    }
}
