<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FlightSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_id',
        'aircraft_id',
        'departure_airport_id',
        'arrival_airport_id',
        'scheduled_departure',
        'scheduled_arrival',
        'base_fare',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_departure' => 'datetime',
            'scheduled_arrival' => 'datetime',
            'base_fare' => 'decimal:2',
        ];
    }

    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }

    public function aircraft(): BelongsTo
    {
        return $this->belongsTo(Aircraft::class);
    }

    public function departureAirport(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'departure_airport_id');
    }

    public function arrivalAirport(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'arrival_airport_id');
    }

    public function gateAssignments(): HasMany
    {
        return $this->hasMany(FlightGateAssignment::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function operation(): HasOne
    {
        return $this->hasOne(FlightOperation::class);
    }

    public function delays(): HasMany
    {
        return $this->hasMany(FlightDelay::class);
    }

    public function cancellations(): HasMany
    {
        return $this->hasMany(FlightCancellation::class);
    }
}
