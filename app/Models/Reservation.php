<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Deligoez\LaravelModelHashId\Traits\HasHashId;
use Deligoez\LaravelModelHashId\Traits\SavesHashId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Deligoez\LaravelModelHashId\Traits\HasHashIdRouting;

class Reservation extends Model
{
    use HasHashId;
    use HasFactory;
    use SavesHashId;
    use HasHashIdRouting;


    protected $guarded = [];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function fromStation(): BelongsTo
    {
        return $this->belongsTo(TripStation::class, 'from_station_id');
    }

    public function toStation(): BelongsTo
    {
        return $this->belongsTo(TripStation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeGetPreviousReservations(
        Builder     $query,
        Trip        $trip,
        TripStation $from,
        TripStation $to,
        array       $select = ['*']
    ): Builder
    {
        return $query
            ->join(
                'trip_stations as from_station',
                'reservations.from_station_id',
                '=',
                'from_station.id'
            )
            ->join(
                'trip_stations as to_station',
                'reservations.to_station_id',
                '=',
                'to_station.id'
            )
            ->join(
                'reservation_seats',
                'reservations.id',
                '=',
                'reservation_seats.reservation_id'
            )
            ->where('reservations.trip_id', $trip->id)
            ->where(function (Builder $query) use ($from, $to) {
                $query->where('from_station.step', '<=', $from->step)
                    ->where('to_station.step', '>=', $to->step);
            })
            ->orWhere(function (Builder $query) use ($from, $to) {
                $query->where('from_station.step', '>=', $from->step)
                    ->where('to_station.step', '<=', $to->step);
            })
            ->select($select);
    }
}
