<?php

namespace Tests\Feature;

use App\Exceptions\AircraftAssignmentConflictException;
use App\Models\Aircraft;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\FlightSchedule;
use App\Services\AircraftAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AircraftAssignmentTest extends TestCase
{
    use RefreshDatabase;

    private Airline $airline;

    private Aircraft $aircraftX;

    private Aircraft $aircraftY;

    private Airport $delhi;

    private Airport $mumbai;

    protected function setUp(): void
    {
        parent::setUp();

        $this->airline = Airline::create([
            'name' => 'Test Air',
            'iata_code' => 'TA',
            'icao_code' => 'TST',
            'country' => 'India',
        ]);

        $this->aircraftX = Aircraft::create([
            'airline_id' => $this->airline->id,
            'registration_number' => 'VT-XXX',
            'manufacturer' => 'Airbus',
            'model' => 'A320',
            'seat_capacity' => 180,
        ]);

        $this->aircraftY = Aircraft::create([
            'airline_id' => $this->airline->id,
            'registration_number' => 'VT-YYY',
            'manufacturer' => 'Boeing',
            'model' => '737',
            'seat_capacity' => 180,
        ]);

        $this->delhi = Airport::create([
            'name' => 'Delhi', 'iata_code' => 'DEL', 'icao_code' => 'VIDP',
            'city' => 'Delhi', 'country' => 'India', 'timezone' => 'Asia/Kolkata',
        ]);

        $this->mumbai = Airport::create([
            'name' => 'Mumbai', 'iata_code' => 'BOM', 'icao_code' => 'VABB',
            'city' => 'Mumbai', 'country' => 'India', 'timezone' => 'Asia/Kolkata',
        ]);
    }

    private function makeSchedule(Aircraft $aircraft, string $number, string $departure, string $arrival): FlightSchedule
    {
        $flight = Flight::create([
            'airline_id' => $this->airline->id,
            'flight_number' => $number,
            'flight_type' => 'domestic',
        ]);

        return FlightSchedule::create([
            'flight_id' => $flight->id,
            'aircraft_id' => $aircraft->id,
            'departure_airport_id' => $this->delhi->id,
            'arrival_airport_id' => $this->mumbai->id,
            'scheduled_departure' => $departure,
            'scheduled_arrival' => $arrival,
            'base_fare' => 5000,
            'status' => 'scheduled',
        ]);
    }

    public function test_overlapping_aircraft_assignment_is_rejected(): void
    {
        $this->makeSchedule($this->aircraftX, 'TA-100', '2026-11-01 10:00', '2026-11-01 12:00');
        $overlapping = $this->makeSchedule($this->aircraftY, 'TA-200', '2026-11-01 11:00', '2026-11-01 13:00');

        $this->expectException(AircraftAssignmentConflictException::class);

        app(AircraftAssignmentService::class)->assign($overlapping, $this->aircraftX);
    }

    public function test_non_overlapping_aircraft_assignment_is_allowed(): void
    {
        $this->makeSchedule($this->aircraftX, 'TA-100', '2026-11-01 10:00', '2026-11-01 12:00');
        $later = $this->makeSchedule($this->aircraftY, 'TA-300', '2026-11-01 13:00', '2026-11-01 15:00');

        $result = app(AircraftAssignmentService::class)->assign($later, $this->aircraftX);

        $this->assertSame($this->aircraftX->id, $result->aircraft_id);
        $this->assertDatabaseHas('flight_schedules', [
            'id' => $later->id,
            'aircraft_id' => $this->aircraftX->id,
        ]);
    }

    public function test_touching_aircraft_windows_do_not_conflict(): void
    {
        $this->makeSchedule($this->aircraftX, 'TA-100', '2026-11-01 10:00', '2026-11-01 12:00');
        $touching = $this->makeSchedule($this->aircraftY, 'TA-400', '2026-11-01 12:00', '2026-11-01 14:00');

        $result = app(AircraftAssignmentService::class)->assign($touching, $this->aircraftX);

        $this->assertSame($this->aircraftX->id, $result->aircraft_id);
    }
}
