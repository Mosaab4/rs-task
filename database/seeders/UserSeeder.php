<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create(['email' => 'test@test.com']);
        User::factory()->create(['email' => 'test2@test.com']);
    }
}
