<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Bus;
use App\Models\City;
use App\Models\Trip;
use App\Models\Seat;
use App\Models\TripStation;
use App\Models\Reservation;
use App\Models\ReservationSeat;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;

class StoreReservationTest extends TestCase
{
    /** @var Collection<TripStation> $trip_stations */
    private Collection $trip_stations;

    private Trip $trip;
    private Bus $bus;

    /** @var Collection<Seat> $seats */
    private Collection $seats;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs($this->user);

        $cities = City::factory()->count(5)->create();

        $this->bus = Bus::factory()->create();

        $this->seats = Seat::factory()
            ->for($this->bus)
            ->count(4)
            ->create();


        $this->trip = Trip::factory()
            ->for($cities->first(), 'fromCity')
            ->for($cities->last(), 'toCity')
            ->for($this->bus, 'bus')
            ->create();

        $this->trip_stations = collect();

        foreach ($cities as $index => $city) {
            $station = TripStation::factory()
                ->for($this->trip)
                ->for($city)
                ->create(['step' => $index + 1]);

            $this->trip_stations->add($station);
        }
    }

    public function test_user_can_reserve_a_seat()
    {
        $this->postJson(route('reservations.store'), [
            'trip_id' => $this->trip->hash_id,
            'from_id' => $this->trip_stations->first()->hash_id,
            'to_id'   => $this->trip_stations->last()->hash_id,
            'seats'   => $this->seats->pluck('hash_id')->toArray()
        ])
            ->assertOk()
            ->assertJsonStructure([
                'status_code',
                'status',
                'message',
                'data'
            ]);

        $this->assertDatabaseHas('reservations', [
            'user_id'         => $this->user->id,
            'trip_id'         => $this->trip->id,
            'from_station_id' => $this->trip_stations->first()->id,
            'to_station_id'   => $this->trip_stations->last()->id,
            'bus_id'          => $this->trip->bus->id
        ]);

        $reservation = Reservation::latest()->first();

        foreach ($this->seats as $seat) {
            $this->assertDatabaseHas('reservation_seats', [
                'seat_id'        => $seat->id,
                'reservation_id' => $reservation->id,
            ]);
        }
    }

    public function test_user_can_not_reserve_a_completed_trip()
    {
        $seats = $this->bus->seats;

        $this->createReservation($seats);

        $this->postJson(route('reservations.store'), [
            'trip_id' => $this->trip->hash_id,
            'from_id' => $this->trip_stations->first()->hash_id,
            'to_id'   => $this->trip_stations->last()->hash_id,
            'seats'   => $seats->pluck('hash_id')->toArray()
        ])
            ->assertBadRequest()
            ->assertJson([
                'data' => [
                    'message' => Lang::get('api.completed_trip')
                ]
            ]);
    }

    public function test_user_can_not_reserve_more_than_available_seats()
    {
        // create new seat
        $additional_seat = Seat::factory()->create();
        $seats = array_merge($this->seats->pluck('hash_id')->toArray(), [$additional_seat->hash_id]);

        $this->postJson(route('reservations.store'), [
            'trip_id' => $this->trip->hash_id,
            'from_id' => $this->trip_stations->first()->hash_id,
            'to_id'   => $this->trip_stations->last()->hash_id,
            'seats'   => $seats
        ])
            ->assertBadRequest()
            ->assertJson([
                'data' => [
                    'message' => Lang::get('api.seats_more_than_available')
                ]
            ]);
    }

    public function test_user_can_not_reserve_already_reserved_seats()
    {
        // create a reservation with this seat
        $seat = $this->bus->seats->first();
        $this->createReservation([$seat]);

        // try to create a reservation using the same seat
        $this->postJson(route('reservations.store'), [
            'trip_id' => $this->trip->hash_id,
            'from_id' => $this->trip_stations->first()->hash_id,
            'to_id'   => $this->trip_stations->last()->hash_id,
            'seats'   => [
                $seat->hash_id,
            ],
        ])
            ->assertBadRequest()
            ->assertJson([
                'data' => [
                    'message' => Lang::get('api.not_available_seats', ['seats' => $seat->hash_id])
                ]
            ]);
    }

    public function test_user_can_not_reserve_more_than_remaining_available_seats()
    {
        // create a reservation with the available seats
        $seats = $this->bus->seats;
        $this->createReservation($seats);

        // create new seats
        Seat::factory()
            ->count(3)
            ->for($this->bus)
            ->create()
            ->pluck('hash_id')
            ->toArray();

        // try to reserve new seats + previous seats
        $this->postJson(route('reservations.store'), [
            'trip_id' => $this->trip->hash_id,
            'from_id' => $this->trip_stations->first()->hash_id,
            'to_id'   => $this->trip_stations->last()->hash_id,
            'seats'   => array_merge($this->bus->seats->pluck('hash_id')->toArray())
        ])
            ->assertBadRequest()
            ->assertJson([
                'data' => [
                    'message' => Lang::get('api.exceeded_available_seats', ['count' => 3])
                ]
            ]);
    }

    public function test_user_can_not_reserve_from_step_less_than_the_destination()
    {
        // reverse the destinations step
        $this->postJson(route('reservations.store'), [
            'trip_id' => $this->trip->hash_id,
            'from_id' => $this->trip_stations->last()->hash_id,
            'to_id'   => $this->trip_stations->first()->hash_id,
            'seats'   => $this->seats->pluck('hash_id')->toArray()
        ])
            ->assertBadRequest()
            ->assertJson([
                'data' => [
                    'message' => Lang::get('api.can_not_reserve_from_this_station')
                ]
            ]);
    }

    public function test_user_can_book_within_stations_if_there_is_available_seats()
    {
        //station    1,2,3,4,5
        //index      0,1,2,3,4
        $stations = $this->trip_stations;

        // Book 2 seats from station 1 to station 4
        $this->createReservation(
            [$this->seats[0], $this->seats[1]],
            $stations[0]->id,
            $stations[3]->id
        );

        // Can Book 2 seats from station 2 to station 3
        $this->postJson(route('reservations.store'), [
            'trip_id' => $this->trip->hash_id,
            'from_id' => $stations[1]->hash_id,
            'to_id'   => $stations[2]->hash_id,
            'seats'   => [
                $this->seats[2]->hash_id,
                $this->seats[3]->hash_id
            ]
        ])
            ->assertOk();

        //Can not book seats from station 1 to station 3
        $this->postJson(route('reservations.store'), [
            'trip_id' => $this->trip->hash_id,
            'from_id' => $this->trip_stations[0]->hash_id,
            'to_id'   => $this->trip_stations[2]->hash_id,
            'seats'   => [
                $this->seats[0]->hash_id,
            ]
        ])
            ->assertBadRequest();

        // Can Book 4 seats from station 4 to station 5
        $this->postJson(route('reservations.store'), [
            'trip_id' => $this->trip->hash_id,
            'from_id' => $stations[3]->hash_id,
            'to_id'   => $stations[4]->hash_id,
            'seats'   => [
                $this->seats[0]->hash_id,
                $this->seats[1]->hash_id,
                $this->seats[2]->hash_id,
                $this->seats[3]->hash_id
            ]
        ])
            ->assertOk();
    }

    private function createReservation($seats, $from =null, $to = null)
    {
        $from = $from != null ? $from : $this->trip_stations->first()->id;;
        $to = $to != null ? $to :$this->trip_stations->last()->id;

        $reservation = Reservation::factory()
            ->for($this->bus)
            ->for($this->trip)
            ->for($this->user)
            ->create([
                'from_station_id' => $from,
                'to_station_id'   => $to,
            ]);

        foreach ($seats as $seat) {
            ReservationSeat::factory()
                ->for($seat)
                ->for($reservation)
                ->create();
        }
    }
}
