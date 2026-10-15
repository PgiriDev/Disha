<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlightDelay extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_schedule_id',
        'delay_minutes',
        'reason',
        'reported_by',
        'reported_at',
    ];

    protected function casts(): array
    {
        return [
            'reported_at' => 'datetime',
        ];
    }

    public function flightSchedule(): BelongsTo
    {
        return $this->belongsTo(FlightSchedule::class);
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'reported_by');
    }
}
