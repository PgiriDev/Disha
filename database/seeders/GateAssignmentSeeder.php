<?php

namespace Database\Seeders;

use App\Models\FlightSchedule;
use App\Models\Gate;
use App\Services\GateAssignmentService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Demonstration data for Module 2 (gate assignments).
 *
 * Uses the real GateAssignmentService so the seed data proves the
 * overlap-prevention logic works. Each gate below is used by one flight only.
 */
class GateAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $assignments = [
            // [departure airport, flight number, gate code]
            ['DEL', '6E-2101', '31'],
            ['DEL', '6E-5301', '32'],
            ['DEL', 'AI-805', '33'],
            ['DEL', 'UK-996', '34'],
            ['DEL', 'UK-737', '35'],
            ['BOM', '6E-2102', '21'],
            ['BOM', 'UK-995', '22'],
        ];

        $service = app(GateAssignmentService::class);

        foreach ($assignments as [$airportIata, $flightNumber, $gateCode]) {
            $schedule = FlightSchedule::query()
                ->whereHas('flight', fn ($query) => $query->where('flight_number', $flightNumber))
                ->whereHas('departureAirport', fn ($query) => $query->where('iata_code', $airportIata))
                ->first();

            if (! $schedule) {
                continue;
            }

            $gate = Gate::query()
                ->where('code', $gateCode)
                ->whereHas('terminal.airport', fn ($query) => $query->where('iata_code', $airportIata))
                ->first();

            if (! $gate) {
                continue;
            }

            $alreadyAssigned = $schedule->gateAssignments()
                ->where('gate_id', $gate->id)
                ->where('status', 'active')
                ->exists();

            if ($alreadyAssigned) {
                continue;
            }

            $service->assign(
                $schedule,
                $gate,
                Carbon::parse($schedule->scheduled_departure)->subMinutes(30)->toDateTimeString(),
                Carbon::parse($schedule->scheduled_arrival)->addMinutes(30)->toDateTimeString(),
            );
        }
    }
}
