<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Deligoez\LaravelModelHashId\Traits\HasHashId;
use Deligoez\LaravelModelHashId\Traits\SavesHashId;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Deligoez\LaravelModelHashId\Traits\HasHashIdRouting;

class Bus extends Model
{
    use HasHashId;
    use HasFactory;
    use SavesHashId;
    use HasHashIdRouting;

    protected $guarded = [];

    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class, 'bus_id');
    }
}
