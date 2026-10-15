<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlightOperation extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_schedule_id',
        'actual_departure',
        'actual_arrival',
        'recorded_by',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'actual_departure' => 'datetime',
            'actual_arrival' => 'datetime',
        ];
    }

    public function flightSchedule(): BelongsTo
    {
        return $this->belongsTo(FlightSchedule::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'recorded_by');
    }
}
