<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Airport extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'iata_code',
        'icao_code',
        'city',
        'country',
        'timezone',
        'latitude',
        'longitude',
        'elevation_ft',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_active' => 'boolean',
        ];
    }

    public function terminals(): HasMany
    {
        return $this->hasMany(Terminal::class);
    }

    public function runways(): HasMany
    {
        return $this->hasMany(Runway::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function departureSchedules(): HasMany
    {
        return $this->hasMany(FlightSchedule::class, 'departure_airport_id');
    }

    public function arrivalSchedules(): HasMany
    {
        return $this->hasMany(FlightSchedule::class, 'arrival_airport_id');
    }
}
