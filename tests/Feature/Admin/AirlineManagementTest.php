<?php

namespace Tests\Feature\Admin;

use App\Models\Aircraft;
use App\Models\Airline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class AirlineManagementTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Lanka Airways',
            'iata_code' => 'LA',
            'icao_code' => 'LAW',
            'country' => 'Sri Lanka',
            'callsign' => 'LANKA',
            'is_active' => '1',
        ], $overrides);
    }

    public function test_index_lists_airlines_and_supports_search(): void
    {
        $admin = $this->admin();
        Airline::create($this->validPayload());
        Airline::create($this->validPayload(['name' => 'Oceanic', 'iata_code' => 'OC', 'icao_code' => 'OCN']));

        $this->actingAs($admin)
            ->get('/admin/airlines')
            ->assertOk()
            ->assertSee('Lanka Airways')
            ->assertSee('Oceanic');

        $this->actingAs($admin)
            ->get('/admin/airlines?search=Oceanic')
            ->assertOk()
            ->assertSee('Oceanic')
            ->assertDontSee('Lanka Airways');
    }

    public function test_admin_can_create_an_airline(): void
    {
        $response = $this->actingAs($this->admin())->post('/admin/airlines', $this->validPayload());

        $response->assertRedirect();
        $this->assertDatabaseHas('airlines', ['iata_code' => 'LA', 'icao_code' => 'LAW']);
    }

    public function test_lowercase_codes_are_normalised_to_uppercase(): void
    {
        $this->actingAs($this->admin())->post('/admin/airlines', $this->validPayload([
            'iata_code' => 'la',
            'icao_code' => 'law',
        ]));

        $this->assertDatabaseHas('airlines', ['iata_code' => 'LA', 'icao_code' => 'LAW']);
    }

    public function test_duplicate_iata_code_is_rejected(): void
    {
        $admin = $this->admin();
        Airline::create($this->validPayload());

        $this->actingAs($admin)
            ->post('/admin/airlines', $this->validPayload(['name' => 'Other', 'icao_code' => 'ZZZ']))
            ->assertSessionHasErrors('iata_code');

        $this->assertDatabaseCount('airlines', 1);
    }

    public function test_duplicate_icao_code_is_rejected(): void
    {
        $admin = $this->admin();
        Airline::create($this->validPayload());

        $this->actingAs($admin)
            ->post('/admin/airlines', $this->validPayload(['name' => 'Other', 'iata_code' => 'ZZ']))
            ->assertSessionHasErrors('icao_code');

        $this->assertDatabaseCount('airlines', 1);
    }

    public function test_airline_can_be_updated(): void
    {
        $airline = Airline::create($this->validPayload());

        $this->actingAs($this->admin())
            ->put("/admin/airlines/{$airline->id}", $this->validPayload(['name' => 'Renamed Air']))
            ->assertRedirect(route('admin.airlines.show', $airline));

        $this->assertDatabaseHas('airlines', ['id' => $airline->id, 'name' => 'Renamed Air']);
    }

    public function test_airline_without_dependents_can_be_deleted(): void
    {
        $airline = Airline::create($this->validPayload());

        $this->actingAs($this->admin())
            ->delete("/admin/airlines/{$airline->id}")
            ->assertRedirect(route('admin.airlines.index'));

        $this->assertDatabaseMissing('airlines', ['id' => $airline->id]);
    }

    public function test_airline_with_aircraft_cannot_be_deleted(): void
    {
        $airline = Airline::create($this->validPayload());
        Aircraft::create([
            'airline_id' => $airline->id,
            'registration_number' => '4R-AAA',
            'manufacturer' => 'Boeing',
            'model' => '737',
            'seat_capacity' => 160,
            'status' => 'active',
        ]);

        $this->actingAs($this->admin())
            ->delete("/admin/airlines/{$airline->id}")
            ->assertSessionHas('error');

        $this->assertDatabaseHas('airlines', ['id' => $airline->id]);
    }

    public function test_validation_rejects_missing_fields(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/airlines', [])
            ->assertSessionHasErrors(['name', 'iata_code', 'icao_code', 'country']);
    }
}
