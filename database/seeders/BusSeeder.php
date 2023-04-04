<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Seat;
use Illuminate\Database\Seeder;

class BusSeeder extends Seeder
{
    public function run(): void
    {
        $buses = Bus::factory()
            ->count(2)
            ->create();

        foreach ($buses as $bus) {
            Seat::factory()
                ->count(12)
                ->for($bus)
                ->create();
        }
    }
}
