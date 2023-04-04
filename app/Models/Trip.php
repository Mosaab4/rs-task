<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Deligoez\LaravelModelHashId\Traits\HasHashId;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Deligoez\LaravelModelHashId\Traits\SavesHashId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Deligoez\LaravelModelHashId\Traits\HasHashIdRouting;

class Trip extends Model
{
    use HasHashId;
    use HasFactory;
    use SavesHashId;
    use HasHashIdRouting;

    protected $guarded = [];

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }

    public function stations(): HasMany
    {
        return $this->hasMany(TripStation::class);
    }

    public function fromCity(): BelongsTo|Trip
    {
        return $this->belongsTo(City::class, 'from_id');
    }

    public function toCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'to_id');
    }
}
