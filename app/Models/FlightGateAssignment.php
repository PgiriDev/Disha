<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlightGateAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_schedule_id',
        'gate_id',
        'assigned_from',
        'assigned_to',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'assigned_from' => 'datetime',
            'assigned_to' => 'datetime',
        ];
    }

    public function flightSchedule(): BelongsTo
    {
        return $this->belongsTo(FlightSchedule::class);
    }

    public function gate(): BelongsTo
    {
        return $this->belongsTo(Gate::class);
    }
}
