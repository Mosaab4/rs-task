<?php

namespace Database\Factories;

use App\Models\Bus;
use App\Models\Trip;
use App\Models\User;
use App\Models\TripStation;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'user_id'         => User::factory(),
            'bus_id'          => Bus::factory(),
            'trip_id'         => Trip::factory(),
            'from_station_id' => TripStation::factory(),
            'to_station_id'   => TripStation::factory(),
        ];
    }
}
