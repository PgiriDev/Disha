<?php

namespace App\Services;

use App\Exceptions\AircraftAssignmentConflictException;
use App\Models\Aircraft;
use App\Models\FlightSchedule;
use Illuminate\Support\Facades\DB;

/**
 * Assigns an aircraft to a flight schedule while preventing the same airframe
 * from being booked for two overlapping time windows.
 *
 * Concurrency: the aircraft row is locked (SELECT ... FOR UPDATE) for the
 * duration of the transaction, and overlapping schedule rows are locked too.
 * A second concurrent request for the same aircraft blocks until the first
 * transaction commits, then sees the new row and is rejected.
 */
class AircraftAssignmentService
{
    public function assign(FlightSchedule $schedule, Aircraft $aircraft): FlightSchedule
    {
        $from = $schedule->scheduled_departure;
        $to = $schedule->scheduled_arrival;

        if ($to === null || $from === null || $to->lessThanOrEqualTo($from)) {
            throw new \InvalidArgumentException(
                'The schedule must have a departure time earlier than its arrival time.'
            );
        }

        return DB::transaction(function () use ($schedule, $aircraft, $from, $to) {
            // Serialise concurrent assignments for this specific airframe.
            Aircraft::query()->whereKey($aircraft->getKey())->lockForUpdate()->first();

            // Two intervals overlap when existing.start < new.end AND existing.end > new.start.
            $hasConflict = FlightSchedule::query()
                ->where('aircraft_id', $aircraft->getKey())
                ->whereKeyNot($schedule->getKey())
                ->where('status', '!=', 'cancelled')
                ->where('scheduled_departure', '<', $to)
                ->where('scheduled_arrival', '>', $from)
                ->lockForUpdate()
                ->exists();

            if ($hasConflict) {
                throw new AircraftAssignmentConflictException(sprintf(
                    'Aircraft %s is already assigned to another flight schedule during %s - %s.',
                    $aircraft->registration_number,
                    $from->toDateTimeString(),
                    $to->toDateTimeString()
                ));
            }

            $schedule->aircraft_id = $aircraft->getKey();
            $schedule->save();

            return $schedule->refresh();
        });
    }
}
