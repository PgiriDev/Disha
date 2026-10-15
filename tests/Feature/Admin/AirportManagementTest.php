<?php

namespace Tests\Feature\Admin;

use App\Models\Airport;
use App\Models\Terminal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class AirportManagementTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Bandaranaike International',
            'iata_code' => 'CMB',
            'icao_code' => 'VCBI',
            'city' => 'Colombo',
            'country' => 'Sri Lanka',
            'timezone' => 'Asia/Colombo',
            'latitude' => '7.1808',
            'longitude' => '79.8841',
            'elevation_ft' => '26',
            'is_active' => '1',
        ], $overrides);
    }

    public function test_admin_can_create_an_airport(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/airports', $this->validPayload())
            ->assertRedirect();

        $this->assertDatabaseHas('airports', ['iata_code' => 'CMB', 'icao_code' => 'VCBI']);
    }

    public function test_duplicate_airport_codes_are_rejected(): void
    {
        $admin = $this->admin();
        Airport::create($this->validPayload());

        $this->actingAs($admin)
            ->post('/admin/airports', $this->validPayload(['name' => 'Other', 'icao_code' => 'ZZZZ']))
            ->assertSessionHasErrors('iata_code');

        $this->actingAs($admin)
            ->post('/admin/airports', $this->validPayload(['name' => 'Other', 'iata_code' => 'ZZZ']))
            ->assertSessionHasErrors('icao_code');
    }

    public function test_airport_codes_must_have_the_correct_length(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/airports', $this->validPayload(['iata_code' => 'CM', 'icao_code' => 'VC']))
            ->assertSessionHasErrors(['iata_code', 'icao_code']);
    }

    public function test_latitude_and_longitude_are_validated(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/airports', $this->validPayload(['latitude' => '120', 'longitude' => '200']))
            ->assertSessionHasErrors(['latitude', 'longitude']);
    }

    public function test_airport_can_be_shown_and_updated(): void
    {
        $airport = Airport::create($this->validPayload());

        $this->actingAs($this->admin())->get("/admin/airports/{$airport->id}")->assertOk();

        $this->actingAs($this->admin())
            ->put("/admin/airports/{$airport->id}", $this->validPayload(['city' => 'Katunayake']))
            ->assertRedirect(route('admin.airports.show', $airport));

        $this->assertDatabaseHas('airports', ['id' => $airport->id, 'city' => 'Katunayake']);
    }

    public function test_airport_with_terminals_cannot_be_deleted(): void
    {
        $airport = Airport::create($this->validPayload());
        Terminal::create(['airport_id' => $airport->id, 'name' => 'T1', 'code' => 'T1']);

        $this->actingAs($this->admin())
            ->delete("/admin/airports/{$airport->id}")
            ->assertSessionHas('error');

        $this->assertDatabaseHas('airports', ['id' => $airport->id]);
    }

    public function test_unused_airport_can_be_deleted(): void
    {
        $airport = Airport::create($this->validPayload());

        $this->actingAs($this->admin())
            ->delete("/admin/airports/{$airport->id}")
            ->assertRedirect(route('admin.airports.index'));

        $this->assertDatabaseMissing('airports', ['id' => $airport->id]);
    }
}
