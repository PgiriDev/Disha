<?php

namespace Tests\Feature\Admin;

use App\Models\Airport;
use App\Models\Gate;
use App\Models\Terminal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class GateManagementTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    private function terminal(): Terminal
    {
        $airport = Airport::create([
            'name' => 'Bandaranaike International',
            'iata_code' => 'CMB',
            'icao_code' => 'VCBI',
            'city' => 'Colombo',
            'country' => 'Sri Lanka',
            'timezone' => 'Asia/Colombo',
        ]);

        return Terminal::create(['airport_id' => $airport->id, 'name' => 'Terminal 1', 'code' => 'T1']);
    }

    public function test_admin_can_create_a_gate(): void
    {
        $terminal = $this->terminal();

        $this->actingAs($this->admin())
            ->post('/admin/gates', [
                'terminal_id' => $terminal->id,
                'code' => 'a1',
                'status' => 'available',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('gates', ['terminal_id' => $terminal->id, 'code' => 'A1']);
    }

    public function test_gate_code_must_be_unique_within_the_terminal(): void
    {
        $terminal = $this->terminal();
        Gate::create(['terminal_id' => $terminal->id, 'code' => 'A1', 'status' => 'available']);

        $this->actingAs($this->admin())
            ->post('/admin/gates', [
                'terminal_id' => $terminal->id,
                'code' => 'A1',
                'status' => 'available',
            ])
            ->assertSessionHasErrors('code');
    }

    public function test_gate_status_must_be_valid(): void
    {
        $terminal = $this->terminal();

        $this->actingAs($this->admin())
            ->post('/admin/gates', [
                'terminal_id' => $terminal->id,
                'code' => 'B2',
                'status' => 'flying',
            ])
            ->assertSessionHasErrors('status');
    }

    public function test_gate_can_be_updated(): void
    {
        $terminal = $this->terminal();
        $gate = Gate::create(['terminal_id' => $terminal->id, 'code' => 'A1', 'status' => 'available']);

        $this->actingAs($this->admin())
            ->put("/admin/gates/{$gate->id}", [
                'terminal_id' => $terminal->id,
                'code' => 'A1',
                'status' => 'closed',
            ])
            ->assertRedirect(route('admin.gates.show', $gate));

        $this->assertDatabaseHas('gates', ['id' => $gate->id, 'status' => 'closed']);
    }

    public function test_gate_can_be_deleted(): void
    {
        $terminal = $this->terminal();
        $gate = Gate::create(['terminal_id' => $terminal->id, 'code' => 'A1', 'status' => 'available']);

        $this->actingAs($this->admin())
            ->delete("/admin/gates/{$gate->id}")
            ->assertRedirect(route('admin.gates.index'));

        $this->assertDatabaseMissing('gates', ['id' => $gate->id]);
    }
}
