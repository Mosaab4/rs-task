<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Trip;
use App\Models\City;
use App\Models\TripStation;
use Illuminate\Database\Seeder;

class TripSeeder extends Seeder
{
    public function run()
    {
        $cairo = City::where(['name' => 'Cairo'])->first();
        $fayyum = City::where(['name' => 'AlFayyum'])->first();
        $minya = City::where(['name' => 'AlMinya'])->first();
        $asyut = City::where(['name' => 'Asyut'])->first();

        $buses = Bus::all();

        $trip = Trip::factory()
            ->for($cairo, 'fromCity')
            ->for($asyut, 'toCity')
            ->for($buses[0], 'bus')
            ->create(['name' => 'Cairo-Asyut']);

        TripStation::factory()
            ->for($trip)
            ->for($cairo)
            ->create(['step' => 1]);

        TripStation::factory()
            ->for($trip)
            ->for($fayyum)
            ->create(['step' => 2]);

        TripStation::factory()
            ->for($trip)
            ->for($minya)
            ->create(['step' => 3]);

        TripStation::factory()
            ->for($trip)
            ->for($asyut)
            ->create(['step' => 4]);

        $mansoura = City::where(['name' => 'Mansoura'])->first();
        $mahalla = City::where(['name' => 'Mahalla'])->first();
        $kafrElShaikh = City::where(['name' => 'KafrElShaikh'])->first();
        $damanhour = City::where(['name' => 'Damanhour'])->first();
        $alexandria = City::where(['name' => 'Alexandria'])->first();

        $trip = Trip::factory()
            ->for($cairo, 'fromCity')
            ->for($asyut, 'toCity')
            ->for($buses[1], 'bus')
            ->create(['name' => 'Mansoura-Alexandria']);

        TripStation::factory()
            ->for($trip)
            ->for($mansoura)
            ->create(['step' => 1]);

        TripStation::factory()
            ->for($trip)
            ->for($mahalla)
            ->create(['step' => 2]);

        TripStation::factory()
            ->for($trip)
            ->for($kafrElShaikh)
            ->create(['step' => 3]);

        TripStation::factory()
            ->for($trip)
            ->for($damanhour)
            ->create(['step' => 4]);

        TripStation::factory()
            ->for($trip)
            ->for($alexandria)
            ->create(['step' => 5]);
    }
}
