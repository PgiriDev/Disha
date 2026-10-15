<?php

namespace Tests\Feature\Admin;

use App\Models\Aircraft;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\FlightSchedule;
use App\Models\Gate;
use App\Models\Runway;
use App\Models\Terminal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_dashboard_reports_live_counts_from_the_database(): void
    {
        Airline::create(['name' => 'Alpha Air', 'iata_code' => 'AA', 'icao_code' => 'AAA', 'country' => 'LK']);
        Airline::create(['name' => 'Beta Air', 'iata_code' => 'BB', 'icao_code' => 'BBB', 'country' => 'LK']);

        $airline = Airline::first();
        $airport = Airport::create([
            'name' => 'Bandaranaike International',
            'iata_code' => 'CMB',
            'icao_code' => 'VCBI',
            'city' => 'Colombo',
            'country' => 'LK',
            'timezone' => 'Asia/Colombo',
        ]);

        Aircraft::create([
            'airline_id' => $airline->id,
            'registration_number' => '4R-ABC',
            'manufacturer' => 'Airbus',
            'model' => 'A320',
            'seat_capacity' => 180,
            'status' => 'active',
        ]);

        $terminal = Terminal::create(['airport_id' => $airport->id, 'name' => 'Terminal 1', 'code' => 'T1']);
        Gate::create(['terminal_id' => $terminal->id, 'code' => 'A1', 'status' => 'available']);
        Runway::create(['airport_id' => $airport->id, 'designation' => '04/22', 'length_m' => 3300, 'surface' => 'asphalt', 'status' => 'active']);

        $flight = Flight::create(['airline_id' => $airline->id, 'flight_number' => 'AA100', 'flight_type' => 'domestic', 'is_active' => true]);
        FlightSchedule::create([
            'flight_id' => $flight->id,
            'aircraft_id' => Aircraft::first()->id,
            'departure_airport_id' => $airport->id,
            'arrival_airport_id' => $airport->id,
            'scheduled_departure' => now()->addHour(),
            'scheduled_arrival' => now()->addHours(2),
            'base_fare' => 15000,
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($this->admin())->get('/admin');

        $response->assertOk();
        $response->assertViewHas('stats', function (array $stats) {
            return $stats['airlines'] === 2
                && $stats['airports'] === 1
                && $stats['aircraft'] === 1
                && $stats['terminals'] === 1
                && $stats['gates'] === 1
                && $stats['runways'] === 1
                && $stats['flights'] === 1
                && $stats['flight_schedules'] === 1;
        });
    }

    public function test_dashboard_shows_empty_state_when_there_are_no_schedules(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin')
            ->assertOk()
            ->assertSee('No flight schedules yet');
    }
}
