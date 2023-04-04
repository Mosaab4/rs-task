<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Deligoez\LaravelModelHashId\Traits\HasHashId;
use Deligoez\LaravelModelHashId\Traits\SavesHashId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Deligoez\LaravelModelHashId\Traits\HasHashIdRouting;

class ReservationSeat extends Model
{
    use HasHashId;
    use HasFactory;
    use SavesHashId;
    use HasHashIdRouting;


    protected $guarded = [];


    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }
}
