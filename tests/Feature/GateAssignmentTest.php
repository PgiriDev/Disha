<?php

namespace Tests\Feature;

use App\Exceptions\GateAssignmentConflictException;
use App\Models\Aircraft;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\FlightSchedule;
use App\Models\Gate;
use App\Models\Terminal;
use App\Services\GateAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GateAssignmentTest extends TestCase
{
    use RefreshDatabase;

    private Airline $airline;

    private Aircraft $aircraft;

    private Airport $delhi;

    private Airport $mumbai;

    private Gate $gateOne;

    private Gate $gateTwo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->airline = Airline::create([
            'name' => 'Test Air',
            'iata_code' => 'TA',
            'icao_code' => 'TST',
            'country' => 'India',
        ]);

        $this->aircraft = Aircraft::create([
            'airline_id' => $this->airline->id,
            'registration_number' => 'VT-XXX',
            'manufacturer' => 'Airbus',
            'model' => 'A320',
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

        $terminal = Terminal::create([
            'airport_id' => $this->delhi->id,
            'name' => 'Terminal 1',
            'code' => 'T1',
        ]);

        $this->gateOne = Gate::create(['terminal_id' => $terminal->id, 'code' => 'A1', 'status' => 'available']);
        $this->gateTwo = Gate::create(['terminal_id' => $terminal->id, 'code' => 'A2', 'status' => 'available']);
    }

    private function makeSchedule(string $number, string $departure, string $arrival): FlightSchedule
    {
        $flight = Flight::create([
            'airline_id' => $this->airline->id,
            'flight_number' => $number,
            'flight_type' => 'domestic',
        ]);

        return FlightSchedule::create([
            'flight_id' => $flight->id,
            'aircraft_id' => $this->aircraft->id,
            'departure_airport_id' => $this->delhi->id,
            'arrival_airport_id' => $this->mumbai->id,
            'scheduled_departure' => $departure,
            'scheduled_arrival' => $arrival,
            'base_fare' => 5000,
            'status' => 'scheduled',
        ]);
    }

    public function test_gate_can_be_assigned_when_free(): void
    {
        $schedule = $this->makeSchedule('TA-100', '2026-11-01 10:00', '2026-11-01 12:00');

        $assignment = app(GateAssignmentService::class)->assign(
            $schedule,
            $this->gateOne,
            '2026-11-01 09:30',
            '2026-11-01 12:30',
        );

        $this->assertDatabaseHas('flight_gate_assignments', [
            'id' => $assignment->id,
            'gate_id' => $this->gateOne->id,
            'status' => 'active',
        ]);
    }

    public function test_overlapping_gate_assignment_is_rejected(): void
    {
        $first = $this->makeSchedule('TA-100', '2026-11-01 10:00', '2026-11-01 12:00');
        $second = $this->makeSchedule('TA-200', '2026-11-01 11:00', '2026-11-01 13:00');

        $service = app(GateAssignmentService::class);
        $service->assign($first, $this->gateOne, '2026-11-01 10:00', '2026-11-01 12:00');

        $this->expectException(GateAssignmentConflictException::class);

        $service->assign($second, $this->gateOne, '2026-11-01 11:00', '2026-11-01 13:00');
    }

    public function test_same_window_on_a_different_gate_is_allowed(): void
    {
        $first = $this->makeSchedule('TA-100', '2026-11-01 10:00', '2026-11-01 12:00');
        $second = $this->makeSchedule('TA-200', '2026-11-01 10:00', '2026-11-01 12:00');

        $service = app(GateAssignmentService::class);
        $service->assign($first, $this->gateOne, '2026-11-01 10:00', '2026-11-01 12:00');
        $assignment = $service->assign($second, $this->gateTwo, '2026-11-01 10:00', '2026-11-01 12:00');

        $this->assertSame($this->gateTwo->id, $assignment->gate_id);
    }

    public function test_released_gate_can_be_reused(): void
    {
        $first = $this->makeSchedule('TA-100', '2026-11-01 10:00', '2026-11-01 12:00');
        $second = $this->makeSchedule('TA-200', '2026-11-01 11:00', '2026-11-01 13:00');

        $service = app(GateAssignmentService::class);
        $assignment = $service->assign($first, $this->gateOne, '2026-11-01 10:00', '2026-11-01 12:00');
        $service->release($assignment);

        $reused = $service->assign($second, $this->gateOne, '2026-11-01 11:00', '2026-11-01 13:00');

        $this->assertSame($this->gateOne->id, $reused->gate_id);
    }
}
