<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlightCancellation extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_schedule_id',
        'reason',
        'cancelled_by',
        'cancelled_at',
        'compensation',
    ];

    protected function casts(): array
    {
        return [
            'cancelled_at' => 'datetime',
            'compensation' => 'decimal:2',
        ];
    }

    public function flightSchedule(): BelongsTo
    {
        return $this->belongsTo(FlightSchedule::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'cancelled_by');
    }
}
