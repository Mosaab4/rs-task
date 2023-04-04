<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Bus;
use App\Models\Trip;
use App\Models\Seat;
use App\Models\City;
use App\Models\TripStation;
use App\Models\Reservation;
use App\Models\ReservationSeat;
use Illuminate\Testing\Fluent\AssertableJson;

class CheckAvailableSeatsTest extends TestCase
{
    private Trip $trip;
    private Bus $bus;

    private TripStation $fromStation;
    private TripStation $toStation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs($this->user);

        $this->bus = Bus::factory()->create();

        Seat::factory()
            ->for($this->bus)
            ->count(4)
            ->create();

        $fromCity = City::factory()->create();
        $toCity = City::factory()->create();

        $this->trip = Trip::factory()
            ->for($fromCity, 'fromCity')
            ->for($toCity, 'toCity')
            ->for($this->bus, 'bus')
            ->create();

        $this->fromStation = TripStation::factory()->for($this->trip)->create(['city_id' => $fromCity->id, 'step' => 1]);
        $this->toStation = TripStation::factory()->for($this->trip)->create(['city_id' => $toCity->id, 'step' => 2]);
    }

    public function test_empty_list_if_all_seats_reserved()
    {
        $seats = $this->bus->seats;

        $this->createReservation($seats);

        $response = $this->postJson(route('check-available-seats'), [
            'trip_id' => $this->trip->hash_id,
            'from_id' => $this->fromStation->hash_id,
            'to_id'   => $this->toStation->hash_id,
        ]);

        $response->assertOk();

        $response->assertJson([
            'data' => []
        ]);
    }

    private function createReservation($seats)
    {
        $reservation = Reservation::factory()
            ->for($this->bus)
            ->for($this->trip)
            ->create([
                'from_station_id' => $this->fromStation->id,
                'to_station_id'   => $this->toStation->id,
            ]);

        foreach ($seats as $seat) {
            ReservationSeat::factory()
                ->for($seat)
                ->for($reservation)
                ->create();
        }
    }

    public function test_only_available_seats_will_appear_but_reserved_seats_will_not_appear()
    {
        $seats = $this->bus->seats;

        $this->createReservation([$seats[0], $seats[1]]);

        $response = $this->postJson(route('check-available-seats'), [
            'trip_id' => $this->trip->hash_id,
            'from_id' => $this->fromStation->hash_id,
            'to_id'   => $this->toStation->hash_id,
        ]);

        $response->assertOk();

        $response->assertJson(function (AssertableJson $json) use ($seats) {
            $json->has('status_code')
                ->has('status')
                ->has('message')
                ->has('data', 2)
                ->has('data.0', function (AssertableJson $json) use ($seats) {
                    $json->where('id', $seats[2]['hash_id'])
                        ->whereNot('id', $seats[0]['hash_id'])
                        ->whereNot('id', $seats[1]['hash_id']);
                })
                ->has('data.1', function (AssertableJson $json) use ($seats) {
                    $json->where('id', $seats[3]['hash_id'])
                        ->whereNot('id', $seats[0]['hash_id'])
                        ->whereNot('id', $seats[1]['hash_id']);
                });
        });
    }

    public function test_available_seats_will_appear_when_no_reservation()
    {
        $seats = $this->bus->seats;

        $response = $this->postJson(route('check-available-seats'), [
            'trip_id' => $this->trip->hash_id,
            'from_id' => $this->fromStation->hash_id,
            'to_id'   => $this->toStation->hash_id,
        ]);

        $response->assertOk();

        $response->assertJson(function (AssertableJson $json) use ($seats) {
            $json->has('status_code')
                ->has('status')
                ->has('message')
                ->has('data', 4)
                ->has('data.0', function (AssertableJson $json) use ($seats) {
                    $json->where('id', $seats[0]['hash_id']);
                })
                ->has('data.1', function (AssertableJson $json) use ($seats) {
                    $json->where('id', $seats[1]['hash_id']);
                })
                ->has('data.2', function (AssertableJson $json) use ($seats) {
                    $json->where('id', $seats[2]['hash_id']);
                })
                ->has('data.3', function (AssertableJson $json) use ($seats) {
                    $json->where('id', $seats[3]['hash_id']);
                });
        });
    }
}
