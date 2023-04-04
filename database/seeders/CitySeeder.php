<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run()
    {
        City::factory()->create(['name' => 'Cairo']);
        City::factory()->create(['name' => 'AlFayyum']);
        City::factory()->create(['name' => 'AlMinya']);
        City::factory()->create(['name' => 'Asyut']);


        City::factory()->create(['name' => 'Mansoura']);
        City::factory()->create(['name' => 'Mahalla']);
        City::factory()->create(['name' => 'KafrElShaikh']);
        City::factory()->create(['name' => 'Damanhour']);
        City::factory()->create(['name' => 'Alexandria']);
    }
}
