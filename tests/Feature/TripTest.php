<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Trip;
use App\Models\Seat;
use App\Models\TripStation;

class TripTest extends TestCase
{
    public function test_user_can_list_trips()
    {
        $trip = Trip::factory()->create();

        $this->actingAs($this->user);

        $this->getJson(route('trip.index'))
            ->assertOk()
            ->assertJsonStructure($this->index_json_structure())
            ->assertJson($this->index_json_data($trip));
    }

    public function test_user_can_view_trip_details()
    {
        $trip = Trip::factory()->create();
        $station = TripStation::factory()->for($trip)->create();
        $seat = Seat::factory()->for($trip->bus)->create();

        $this->actingAs($this->user);

        $this->getJson(route('trip.show', $trip))
            ->assertOk()
            ->assertJsonStructure($this->show_json_structure())
            ->assertJson($this->show_json_data($trip, $station, $seat));
    }

    private function index_json_structure(): array
    {
        return [
            'status_code',
            'status',
            'data' => [
                [
                    'id',
                    'name',
                    'from_city' => [
                        'id',
                        'name'
                    ],
                    'to_city'   => [
                        'id',
                        'name'
                    ]
                ]
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'per_page',
                'to',
                'total'
            ]
        ];
    }

    private function index_json_data(Trip $trip): array
    {
        return [
            'data' => [
                [
                    'id'        => $trip->hash_id,
                    'name'      => $trip->name,
                    'from_city' => [
                        'id'   => $trip->fromCity->hash_id,
                        'name' => $trip->fromCity->name
                    ],
                    'to_city'   => [
                        'id'   => $trip->toCity->hash_id,
                        'name' => $trip->toCity->name
                    ]
                ]
            ]
        ];
    }

    private function show_json_structure(): array
    {
        return [
            'status_code',
            'status',
            'data' => [
                'id',
                'name',
                'from_city' => [
                    'id',
                    'name'
                ],
                'to_city'   => [
                    'id',
                    'name'
                ],
                'bus'       => [
                    'id',
                    'name',
                    'seats' => [
                        [
                            'id'
                        ]
                    ]
                ],
                'stations'  => [
                    [
                        'id',
                        'step',
                        'city' => [
                            'id',
                            'name'
                        ]
                    ]
                ]
            ]
        ];
    }

    private function show_json_data(Trip $trip, TripStation $station, Seat $seat): array
    {
        return [
            'data' => [
                'id'        => $trip->hash_id,
                'name'      => $trip->name,
                'from_city' => [
                    'id'   => $trip->fromCity->hash_id,
                    'name' => $trip->fromCity->name
                ],
                'to_city'   => [
                    'id'   => $trip->toCity->hash_id,
                    'name' => $trip->toCity->name
                ],
                'bus'       => [
                    'id'    => $trip->bus->hash_id,
                    'name'  => $trip->bus->name,
                    'seats' => [
                        [
                            'id' => $seat->hash_id
                        ]
                    ]
                ],
                'stations'  => [
                    [
                        'id'   => $station->hash_id,
                        'step' => $station->step,
                        'city' => [
                            'id'   => $station->city->hash_id,
                            'name' => $station->city->name
                        ]
                    ]
                ]
            ]
        ];
    }
}
