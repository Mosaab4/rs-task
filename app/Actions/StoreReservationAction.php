<?php

namespace App\Actions;

use App\Models\Trip;
use App\Models\Seat;
use App\Models\TripStation;
use App\Models\Reservation;
use App\Models\ReservationSeat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Lang;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Requests\StoreReservationRequest;

class StoreReservationAction
{
    private Trip $trip;
    private TripStation $from;
    private TripStation $to;

    private int $reserved_seats_count;

    public function __construct(
        public StoreReservationRequest $request
    )
    {
        $this->trip = Trip::where('hash_id', $request['trip_id'])->first();
        $this->from = TripStation::where('hash_id', $request['from_id'])->first();
        $this->to = TripStation::where('hash_id', $request['to_id'])->first();
        $this->reserved_seats_count = $this->getReservedSeatsFromPreviousTrips();
    }

    public function execute(): array
    {
//        Log::info($this->request->all());
        if ($this->stationsStepsValid()) {
            return [false, Lang::get('api.can_not_reserve_from_this_station')];
        }

        $bus_seats_count = Seat::where('bus_id', $this->trip->bus_id)->count();

        if ($this->tripIsCompleted($bus_seats_count)) {
            return [false, Lang::get('api.completed_trip')];
        }

        if ($this->requestedSeatsMoreThanAvailable($bus_seats_count)) {
            return [false, Lang::get('api.seats_more_than_available')];
        }

        if ($this->requestedSeatsExceededAvailable($bus_seats_count)) {
            return [false, Lang::get('api.exceeded_available_seats', ['count' => $bus_seats_count - $this->reserved_seats_count])];
        }

        [$already_booked, $message, $requested_seat_ids] = $this->ifRequestedSeatsAlreadyBooked();

        if (!$already_booked) {
            return [false, $message];
        }

        $reservation = $this->createReservation();

        $this->createSeats($reservation, $requested_seat_ids);


        return [true, "Success"];
    }

    private function getReservedSeatsFromPreviousTrips()
    {
        return Reservation::query()
            ->getPreviousReservations(
                trip: $this->trip,
                from: $this->from,
                to: $this->to,
                select: [
                    'to_station.id as to_id',
                    'to_station.step as to_step',
                    'from_station.id as from_id',
                    'from_station.step as from_step',
                    DB::raw('count(*) as count'),
                ])
            ->groupBy(['to_id', 'from_id'])
            ->get()
            ->sum('count');
    }

    private function tripIsCompleted(int $bus_seats_count): bool
    {
        return $this->reserved_seats_count >= $bus_seats_count;
    }

    private function requestedSeatsMoreThanAvailable(int $bus_seats_count): bool
    {
        return count($this->request['seats']) > $bus_seats_count;
    }

    private function requestedSeatsExceededAvailable(int $bus_seats_count): bool
    {
        $remaining_seats_count = $bus_seats_count - $this->reserved_seats_count;

        return count($this->request['seats']) > $remaining_seats_count;
    }

    private function stationsStepsValid(): bool
    {
        return $this->from->step >= $this->to->step;
    }

    private function ifRequestedSeatsAlreadyBooked(): array
    {
        $requested_seat_ids = $this->getRequestedSeatsIds();

        $trip_reserved_seats = $this->getTripReservedSeats($requested_seat_ids);

        $booked_seats = [];
        foreach ($trip_reserved_seats as $seat) {
            if (in_array($seat->seat_id, $requested_seat_ids)) {
                $booked_seats [] = $seat->seat->hash_id;
            }
        }

        if (!empty($booked_seats)) {
            return [false, Lang::get('api.not_available_seats', ['seats' => implode(', ', $booked_seats)]), null];
        }

        return [true, "", $requested_seat_ids];
    }

    private function getRequestedSeatsIds(): array
    {
        return Seat::query()
            ->whereIn('hash_id', $this->request['seats'])
            ->select(['id'])
            ->get()
            ->pluck('id')
            ->toArray();
    }

    public function getTripReservedSeats($requested_seat_ids): Collection
    {
        return ReservationSeat::query()
            ->join(
                'reservations',
                'reservations.id',
                '=',
                'reservation_seats.reservation_id'
            )
            ->where('trip_id', $this->trip->id)
            ->where('from_station_id', $this->from->id)
            ->where('to_station_id', $this->to->id)
            ->whereIn('seat_id', $requested_seat_ids)
            ->get();
    }

    private function createReservation(): Reservation
    {
        return Reservation::create([
            'user_id'         => auth()->id(),
            'trip_id'         => $this->trip->id,
            'to_station_id'   => $this->to->id,
            'from_station_id' => $this->from->id,
            'bus_id'          => $this->trip->bus_id
        ]);
    }

    private function createSeats(Reservation $reservation, array $requested_seat_ids): void
    {
        foreach ($requested_seat_ids as $seat) {
            ReservationSeat::create([
                'seat_id'        => $seat,
                'reservation_id' => $reservation->id,
            ]);
        }
    }
}
