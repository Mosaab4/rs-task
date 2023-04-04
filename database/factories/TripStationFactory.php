<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\City;
use App\Models\Trip;
use App\Models\TripStation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripStationFactory extends Factory
{
    protected $model = TripStation::class;

    public function definition(): array
    {
        return [
            'city_id' => City::factory(),
            'trip_id' => Trip::factory(),
            'step'    => $this->faker->numberBetween(1, 10)
        ];
    }
}
