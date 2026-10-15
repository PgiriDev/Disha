<?php

namespace Database\Seeders;

use App\Models\Aircraft;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\FlightSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Demonstration data for Module 2 (flight definitions and schedules).
 *
 * Aircraft are deliberately scheduled in non-overlapping windows so the
 * AircraftAssignmentService conflict rule is respected by the seed data.
 */
class FlightScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $flights = [
            // [airline, flight number, type, departure, arrival, registration, departure time, arrival time, base fare]
            ['6E', '6E-2101', 'domestic', 'DEL', 'BOM', 'VT-IND', '2026-11-01 06:00', '2026-11-01 08:10', 4700.00],
            ['6E', '6E-2102', 'domestic', 'BOM', 'DEL', 'VT-IND', '2026-11-01 09:00', '2026-11-01 11:10', 4700.00],
            ['6E', '6E-5301', 'domestic', 'DEL', 'BLR', 'VT-INA', '2026-11-01 06:30', '2026-11-01 09:15', 5200.00],
            ['6E', '6E-5302', 'domestic', 'BLR', 'DEL', 'VT-INA', '2026-11-01 10:00', '2026-11-01 12:45', 5200.00],
            ['AI', 'AI-805', 'domestic', 'DEL', 'BLR', 'VT-AIA', '2026-11-01 07:00', '2026-11-01 09:50', 6100.00],
            ['AI', 'AI-806', 'domestic', 'BLR', 'BOM', 'VT-AIA', '2026-11-01 11:00', '2026-11-01 12:45', 5800.00],
            ['AI', 'AI-101', 'domestic', 'DEL', 'BOM', 'VT-AIB', '2026-11-01 13:00', '2026-11-01 15:10', 7200.00],
            ['UK', 'UK-995', 'domestic', 'BOM', 'DEL', 'VT-VTA', '2026-11-01 06:15', '2026-11-01 08:25', 4900.00],
            ['UK', 'UK-996', 'domestic', 'DEL', 'CCU', 'VT-VTA', '2026-11-01 09:30', '2026-11-01 11:45', 5500.00],
            ['UK', 'UK-737', 'domestic', 'DEL', 'MAA', 'VT-VTB', '2026-11-01 12:30', '2026-11-01 15:05', 5400.00],
        ];

        foreach ($flights as [$airlineIata, $number, $type, $from, $to, $registration, $departure, $arrival, $fare]) {
            $airline = Airline::query()->where('iata_code', $airlineIata)->firstOrFail();
            $aircraft = Aircraft::query()->where('registration_number', $registration)->firstOrFail();
            $departureAirport = Airport::query()->where('iata_code', $from)->firstOrFail();
            $arrivalAirport = Airport::query()->where('iata_code', $to)->firstOrFail();

            $flight = Flight::query()->updateOrCreate(
                ['airline_id' => $airline->id, 'flight_number' => $number],
                ['flight_type' => $type, 'is_active' => true]
            );

            FlightSchedule::query()->updateOrCreate(
                [
                    'flight_id' => $flight->id,
                    'scheduled_departure' => Carbon::parse($departure),
                ],
                [
                    'aircraft_id' => $aircraft->id,
                    'departure_airport_id' => $departureAirport->id,
                    'arrival_airport_id' => $arrivalAirport->id,
                    'scheduled_arrival' => Carbon::parse($arrival),
                    'base_fare' => $fare,
                    'status' => 'scheduled',
                ]
            );
        }
    }
}
