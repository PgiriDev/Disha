<?php

namespace Tests\Feature\Admin;

use App\Models\Airport;
use App\Models\Runway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class RunwayManagementTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    private function airport(string $iata = 'CMB', string $icao = 'VCBI'): Airport
    {
        return Airport::create([
            'name' => 'Airport '.$iata,
            'iata_code' => $iata,
            'icao_code' => $icao,
            'city' => 'City',
            'country' => 'Country',
            'timezone' => 'Asia/Colombo',
        ]);
    }

    private function validPayload(Airport $airport, array $overrides = []): array
    {
        return array_merge([
            'airport_id' => $airport->id,
            'designation' => '04/22',
            'length_m' => 3300,
            'width_m' => 45,
            'surface' => 'asphalt',
            'status' => 'active',
        ], $overrides);
    }

    public function test_admin_can_create_a_runway(): void
    {
        $airport = $this->airport();

        $this->actingAs($this->admin())
            ->post('/admin/runways', $this->validPayload($airport))
            ->assertRedirect();

        $this->assertDatabaseHas('runways', ['airport_id' => $airport->id, 'designation' => '04/22']);
    }

    public function test_runway_designation_must_be_unique_within_the_airport(): void
    {
        $airport = $this->airport();
        Runway::create($this->validPayload($airport));

        $this->actingAs($this->admin())
            ->post('/admin/runways', $this->validPayload($airport))
            ->assertSessionHasErrors('designation');
    }

    public function test_runway_length_must_be_positive(): void
    {
        $airport = $this->airport();

        $this->actingAs($this->admin())
            ->post('/admin/runways', $this->validPayload($airport, ['length_m' => 0]))
            ->assertSessionHasErrors('length_m');
    }

    public function test_runway_can_be_updated_and_deleted(): void
    {
        $airport = $this->airport();
        $runway = Runway::create($this->validPayload($airport));

        $this->actingAs($this->admin())
            ->put("/admin/runways/{$runway->id}", $this->validPayload($airport, ['status' => 'maintenance']))
            ->assertRedirect(route('admin.runways.show', $runway));

        $this->assertDatabaseHas('runways', ['id' => $runway->id, 'status' => 'maintenance']);

        $this->actingAs($this->admin())
            ->delete("/admin/runways/{$runway->id}")
            ->assertRedirect(route('admin.runways.index'));

        $this->assertDatabaseMissing('runways', ['id' => $runway->id]);
    }
}
