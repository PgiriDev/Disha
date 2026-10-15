<?php

namespace Tests\Feature\Admin;

use App\Models\Aircraft;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\FlightSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class AircraftManagementTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    private ?Airline $cachedAirline = null;

    private function airline(): Airline
    {
        return $this->cachedAirline ??= Airline::create([
            'name' => 'Lanka Airways',
            'iata_code' => 'LA',
            'icao_code' => 'LAW',
            'country' => 'Sri Lanka',
        ]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'airline_id' => $this->airline()->id,
            'registration_number' => '4r-abc',
            'manufacturer' => 'Airbus',
            'model' => 'A320',
            'seat_capacity' => 180,
            'business_capacity' => 20,
            'economy_capacity' => 140,
            'status' => 'active',
        ], $overrides);
    }

    public function test_admin_can_create_an_aircraft_and_registration_is_uppercased(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/aircraft', $this->validPayload())
            ->assertRedirect();

        $this->assertDatabaseHas('aircraft', ['registration_number' => '4R-ABC']);
    }

    public function test_invalid_airline_foreign_key_is_rejected(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/aircraft', $this->validPayload(['airline_id' => 9999]))
            ->assertSessionHasErrors('airline_id');

        $this->assertDatabaseCount('aircraft', 0);
    }

    public function test_business_and_economy_capacity_cannot_exceed_total_seats(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/aircraft', $this->validPayload([
                'seat_capacity' => 100,
                'business_capacity' => 60,
                'economy_capacity' => 60,
            ]))
            ->assertSessionHasErrors('business_capacity');

        $this->assertDatabaseCount('aircraft', 0);
    }

    public function test_duplicate_registration_number_is_rejected(): void
    {
        $payload = $this->validPayload(['registration_number' => '4R-ABC']);
        Aircraft::create($payload);

        $this->actingAs($this->admin())
            ->post('/admin/aircraft', $this->validPayload(['registration_number' => '4R-ABC']))
            ->assertSessionHasErrors('registration_number');
    }

    public function test_aircraft_can_be_updated(): void
    {
        $aircraft = Aircraft::create($this->validPayload());

        $this->actingAs($this->admin())
            ->put("/admin/aircraft/{$aircraft->id}", $this->validPayload([
                'registration_number' => '4R-ABC',
                'status' => 'maintenance',
            ]))
            ->assertRedirect(route('admin.aircraft.show', $aircraft));

        $this->assertDatabaseHas('aircraft', ['id' => $aircraft->id, 'status' => 'maintenance']);
    }

    public function test_aircraft_assigned_to_a_schedule_cannot_be_deleted(): void
    {
        $aircraft = Aircraft::create($this->validPayload());

        $airport = Airport::create([
            'name' => 'Bandaranaike International',
            'iata_code' => 'CMB',
            'icao_code' => 'VCBI',
            'city' => 'Colombo',
            'country' => 'Sri Lanka',
            'timezone' => 'Asia/Colombo',
        ]);

        $flight = Flight::create([
            'airline_id' => $aircraft->airline_id,
            'flight_number' => 'LA100',
            'flight_type' => 'domestic',
            'is_active' => true,
        ]);

        FlightSchedule::create([
            'flight_id' => $flight->id,
            'aircraft_id' => $aircraft->id,
            'departure_airport_id' => $airport->id,
            'arrival_airport_id' => $airport->id,
            'scheduled_departure' => now()->addHour(),
            'scheduled_arrival' => now()->addHours(2),
            'base_fare' => 10000,
            'status' => 'scheduled',
        ]);

        $this->actingAs($this->admin())
            ->delete("/admin/aircraft/{$aircraft->id}")
            ->assertSessionHas('error');

        $this->assertDatabaseHas('aircraft', ['id' => $aircraft->id]);
    }

    public function test_unused_aircraft_can_be_deleted(): void
    {
        $aircraft = Aircraft::create($this->validPayload());

        $this->actingAs($this->admin())
            ->delete("/admin/aircraft/{$aircraft->id}")
            ->assertRedirect(route('admin.aircraft.index'));

        $this->assertDatabaseMissing('aircraft', ['id' => $aircraft->id]);
    }
}
