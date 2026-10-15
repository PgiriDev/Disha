<?php

namespace App\Services;

use App\Exceptions\GateAssignmentConflictException;
use App\Models\FlightGateAssignment;
use App\Models\FlightSchedule;
use App\Models\Gate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Assigns a gate to a flight schedule while preventing overlapping use of the
 * same gate. Gate availability is derived from time-based assignments (there is
 * no stored "is_available" flag), so a gate is free exactly when no active
 * assignment overlaps the requested window.
 *
 * Concurrency: the gate row is locked (SELECT ... FOR UPDATE) and overlapping
 * assignment rows are locked inside a single transaction, so two concurrent
 * requests cannot both claim the same gate window.
 */
class GateAssignmentService
{
    public function assign(
        FlightSchedule $schedule,
        Gate $gate,
        ?string $from = null,
        ?string $to = null,
    ): FlightGateAssignment {
        $windowStart = $from ? Carbon::parse($from) : $schedule->scheduled_departure;
        $windowEnd = $to ? Carbon::parse($to) : $schedule->scheduled_arrival;

        if ($windowStart === null || $windowEnd === null || $windowEnd->lessThanOrEqualTo($windowStart)) {
            throw new \InvalidArgumentException(
                'The gate assignment window must have a start earlier than its end.'
            );
        }

        return DB::transaction(function () use ($schedule, $gate, $windowStart, $windowEnd) {
            // Serialise concurrent assignments for this specific gate.
            Gate::query()->whereKey($gate->getKey())->lockForUpdate()->first();

            // An open-ended assignment (assigned_to = null) is treated as
            // occupying the gate indefinitely.
            $hasConflict = FlightGateAssignment::query()
                ->where('gate_id', $gate->getKey())
                ->where('status', 'active')
                ->where('assigned_from', '<', $windowEnd)
                ->where(function ($query) use ($windowStart) {
                    $query->whereNull('assigned_to')
                        ->orWhere('assigned_to', '>', $windowStart);
                })
                ->lockForUpdate()
                ->exists();

            if ($hasConflict) {
                throw new GateAssignmentConflictException(sprintf(
                    'Gate %s is already occupied during %s - %s.',
                    $gate->code,
                    $windowStart->toDateTimeString(),
                    $windowEnd->toDateTimeString()
                ));
            }

            return FlightGateAssignment::create([
                'flight_schedule_id' => $schedule->getKey(),
                'gate_id' => $gate->getKey(),
                'assigned_from' => $windowStart,
                'assigned_to' => $windowEnd,
                'status' => 'active',
            ]);
        });
    }

    /**
     * Release a gate assignment so the gate window becomes available again.
     */
    public function release(FlightGateAssignment $assignment): FlightGateAssignment
    {
        $assignment->update(['status' => 'released']);

        return $assignment->refresh();
    }
}
