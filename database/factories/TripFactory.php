<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Bus;
use App\Models\Trip;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'from_id' => City::factory(),
            'to_id' => City::factory(),
            'bus_id' => Bus::factory(),
        ];
    }
}
