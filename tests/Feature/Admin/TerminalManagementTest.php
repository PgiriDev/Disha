<?php

namespace Tests\Feature\Admin;

use App\Models\Airport;
use App\Models\Gate;
use App\Models\Terminal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class TerminalManagementTest extends TestCase
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

    public function test_admin_can_create_a_terminal(): void
    {
        $airport = $this->airport();

        $this->actingAs($this->admin())
            ->post('/admin/terminals', [
                'airport_id' => $airport->id,
                'name' => 'Terminal 1',
                'code' => 't1',
                'passenger_capacity' => 5000,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('terminals', ['airport_id' => $airport->id, 'code' => 'T1']);
    }

    public function test_terminal_code_must_be_unique_within_the_same_airport(): void
    {
        $airport = $this->airport();
        Terminal::create(['airport_id' => $airport->id, 'name' => 'T1', 'code' => 'T1']);

        $this->actingAs($this->admin())
            ->post('/admin/terminals', [
                'airport_id' => $airport->id,
                'name' => 'Duplicate',
                'code' => 'T1',
            ])
            ->assertSessionHasErrors('code');
    }

    public function test_same_terminal_code_is_allowed_at_a_different_airport(): void
    {
        $first = $this->airport('CMB', 'VCBI');
        $second = $this->airport('BIA', 'VCCC');
        Terminal::create(['airport_id' => $first->id, 'name' => 'T1', 'code' => 'T1']);

        $this->actingAs($this->admin())
            ->post('/admin/terminals', [
                'airport_id' => $second->id,
                'name' => 'T1',
                'code' => 'T1',
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('terminals', 2);
    }

    public function test_terminal_with_gates_cannot_be_deleted(): void
    {
        $airport = $this->airport();
        $terminal = Terminal::create(['airport_id' => $airport->id, 'name' => 'T1', 'code' => 'T1']);
        Gate::create(['terminal_id' => $terminal->id, 'code' => 'A1', 'status' => 'available']);

        $this->actingAs($this->admin())
            ->delete("/admin/terminals/{$terminal->id}")
            ->assertSessionHas('error');

        $this->assertDatabaseHas('terminals', ['id' => $terminal->id]);
    }

    public function test_unused_terminal_can_be_deleted(): void
    {
        $airport = $this->airport();
        $terminal = Terminal::create(['airport_id' => $airport->id, 'name' => 'T1', 'code' => 'T1']);

        $this->actingAs($this->admin())
            ->delete("/admin/terminals/{$terminal->id}")
            ->assertRedirect(route('admin.terminals.index'));

        $this->assertDatabaseMissing('terminals', ['id' => $terminal->id]);
    }

    public function test_invalid_airport_foreign_key_is_rejected(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/terminals', [
                'airport_id' => 9999,
                'name' => 'Ghost',
                'code' => 'GH',
            ])
            ->assertSessionHasErrors('airport_id');
    }
}
