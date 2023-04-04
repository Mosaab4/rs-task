<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Bus;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusFactory extends Factory
{
    protected $model = Bus::class;

    public function definition(): array
    {
        return [
            'name' => "Bus - " . $this->faker->numberBetween(1, 1000),
        ];
    }
}
