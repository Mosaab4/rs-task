<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Trip;
use App\Models\Seat;
use App\Models\TripStation;
use App\Models\Reservation;
use App\Http\Controllers\Controller;
use App\Http\Resources\SeatResource;
use App\Http\Requests\CheckAvailableSeatsRequest;

class CheckAvailableSeatsController extends Controller
{
    public function __invoke(CheckAvailableSeatsRequest $request)
    {
        $trip = Trip::where('hash_id', $request['trip_id'])->first();
        $from = TripStation::where('hash_id', $request['from_id'])->first();
        $to = TripStation::where('hash_id', $request['to_id'])->first();

        $reserved_seats = Reservation::query()
            ->getPreviousReservations($trip, $from, $to, ['seat_id'])
            ->get()
            ->pluck('seat_id')
            ->toArray();

        $seats = Seat::where('bus_id', $trip->bus_id)
            ->whereNotIn('id', $reserved_seats)
            ->select(['id', 'hash_id'])
            ->get();

        return $this->respondWithSuccess(SeatResource::collection($seats), "Available Seats");
    }
}
