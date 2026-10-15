<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aircraft extends Model
{
    use HasFactory;

    /**
     * "aircraft" is uncountable, so Eloquent cannot infer the table name.
     */
    protected $table = 'aircraft';

    protected $fillable = [
        'airline_id',
        'registration_number',
        'manufacturer',
        'model',
        'seat_capacity',
        'business_capacity',
        'economy_capacity',
        'status',
    ];

    public function airline(): BelongsTo
    {
        return $this->belongsTo(Airline::class);
    }

    public function flightSchedules(): HasMany
    {
        return $this->hasMany(FlightSchedule::class);
    }
}
