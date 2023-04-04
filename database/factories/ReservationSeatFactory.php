<?php

namespace Database\Factories;

use App\Models\Seat;
use App\Models\Reservation;
use App\Models\ReservationSeat;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationSeatFactory extends Factory
{
    protected $model = ReservationSeat::class;

    public function definition(): array
    {
        return [
            'seat_id'        => Seat::factory(),
            'reservation_id' => Reservation::factory(),
        ];
    }
}
